<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseUnitProgrammeMapping;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{
    public function generateIcs(Request $request)
    {
        // Get active academic session
        $currentAcademicSession = \App\Models\AcademicSession::where('status', 'active')->first();
        
        if (!$currentAcademicSession) {
            return response('No active academic session.', 404);
        }

        if (!$request->filled(['programme_id', 'level'])) {
            return response('Missing parameters.', 400);
        }

        list($yearId, $semesterId) = explode('.', $request->level);
        
        $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
            ->where('year_of_study_id', $yearId)
            ->where('semester_id', $semesterId)
            ->where('academic_session_id', $currentAcademicSession->id)
            ->with(['courseUnit', 'lecturer', 'day'])
            ->get();

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//Open University of Kenya//Timetable System//EN\r\n";
        $icsContent .= "CALSCALE:GREGORIAN\r\n";
        $icsContent .= "X-WR-CALNAME:OUK Timetable\r\n";
        $icsContent .= "X-WR-TIMEZONE:Africa/Nairobi\r\n";

        // Generate recurring events for each timetable entry for 14 weeks
        foreach ($timetable as $lesson) {
            // Find the next occurrence of this day of the week
            $dayOfWeek = strtoupper(substr($lesson->day->name, 0, 2)); // e.g., MO, TU, WE
            $dayName = $lesson->day->name; // e.g., Monday
            
            $startDate = Carbon::parse('next ' . $dayName)->setTimezone('Africa/Nairobi');
            
            if ($lesson->morning_start_time) {
                $start = Carbon::parse($lesson->morning_start_time);
                $end = $start->copy()->addMinutes($lesson->morning_duration);
            } elseif ($lesson->evening_start_time) {
                $start = Carbon::parse($lesson->evening_start_time);
                $end = $start->copy()->addMinutes($lesson->evening_duration);
            } else {
                continue; // Skip if no time
            }
            
            $eventStart = $startDate->copy()->setTime($start->hour, $start->minute);
            $eventEnd = $startDate->copy()->setTime($end->hour, $end->minute);
            
            $uid = md5($lesson->id . $eventStart->toDateTimeString()) . "@ouk.ac.ke";
            $dtstamp = gmdate('Ymd\THis\Z');
            
            $icsContent .= "BEGIN:VEVENT\r\n";
            $icsContent .= "UID:" . $uid . "\r\n";
            $icsContent .= "DTSTAMP:" . $dtstamp . "\r\n";
            $icsContent .= "DTSTART;TZID=Africa/Nairobi:" . $eventStart->format('Ymd\THis') . "\r\n";
            $icsContent .= "DTEND;TZID=Africa/Nairobi:" . $eventEnd->format('Ymd\THis') . "\r\n";
            $icsContent .= "RRULE:FREQ=WEEKLY;COUNT=14;BYDAY=" . $dayOfWeek . "\r\n"; // 14 weeks of classes
            $icsContent .= "SUMMARY:" . $lesson->courseUnit->code . " - " . $lesson->courseUnit->name . "\r\n";
            
            $instructor = $lesson->lecturer ? $lesson->lecturer->name : 'TBA';
            $icsContent .= "DESCRIPTION:Instructor: " . $instructor . "\\nProgramme: " . $lesson->programme->name . "\r\n";
            $icsContent .= "END:VEVENT\r\n";
        }

        $icsContent .= "END:VCALENDAR\r\n";

        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="timetable.ics"');
    }
}
