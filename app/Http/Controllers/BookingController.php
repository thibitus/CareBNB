<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input, Response, View;
use Session;
use DB;
use DateTime;

use App\Models\Appointment;
use App\Models\BookingDateTime;
use App\Models\Hulpverleners;
use App\Models\User;


class BookingController extends Controller
{

  /**
  * Function to retrieve the index page
  * User selects package to continue
  *
*/
 public function getIndex()
 {
   return view('index/index');
 }

  /**
  * Function to retrieve datepicker
  *
  * User selects date + time to continue
  **/
  public function getCalendar($hid)
  {


    $hulpverlener = User::find($hid);
    Session::put('userID', $hid);
    // This groups all booking times by date so we can give a list of all days available.
    $data = [
    'hulpverlenerNaam' => $hulpverlener->name,
    'hulpverlenerAchterNaam' => $hulpverlener->lastname,
    'days' => BookingDateTime::all()
    ];

    return view('BookAppointment', $data);
  }

  /**
  * Function to get customer details after Date & Time pick
  *
  **/
  public function getDetails($aptID)
  {

    // Put the passed date time ID into the session
    Session::put('aptID', $aptID);
    $hulpverlener = User::find(Session::get('userID'));

    // Get row of date id
    $dateRow = BookingDateTime::find($aptID);
    $dateFormat = new DateTime($dateRow->booking_datetime);
    $dateFormat = $dateFormat->format('g:i a \o\n l, jS \o\f F Y');
    Session::put('selection', $dateRow->booking_datetime);

    $data = [
    'hid' => Session::get('hulpverlener_id'),
    'hulpverlenerNaam' => $hulpverlener->name,
    'dateRow'   => $dateRow,
    'dateFormat' => $dateFormat,
    'aptID' =>  $aptID,
    ];

    return view('customerInfo', $data);
  }

  /**
  * Function to post customer info and present confirmation view
  * User Confirms appointment details to continue
  **/
  public function anyConfirm()
  {

    $input = Input::all();
    $hulpverlener = User::find(Session::get('userID'));
    $appointmentInfo = [
      "hid"   => Session::get('userID'),
      "hulpverlener_naam" => $hulpverlener->name,
      "datetime"     => Session::get('selection'),
      "fname"        => $input['fname'],
      "lname"        => $input['lname'],
      "number"       => $input['number'],
      "email"        => $input['email'],
      "updates"      => isset($input['newsletterBox']) ? 'Yes' : 'No'
      ];

    Session::put('appointmentInfo', $appointmentInfo);


    if(isset($input['newsletterBox'])) {
      Session::put('updates', '1');
    } else {
      Session::put('updates', '0');
    }

    return View::make('confirm')->with('appointmentInfo', $appointmentInfo);
  }

  /**
   * Function to create the appointment, scrub the database, and send out an email confirmation
   *
   * User interaction is complete
   *
   **/
  public function anyConfirmed()
  {

    // When this boolean is set to True, instead of deleting all appointment times for the package duration
    // It will instead remove all times up to the end of the day, and continue to the next day until the package
    // time is done.
    $overlapDays = FALSE;
    $info = Session::get('appointmentInfo');
    $startTime = new DateTime($info['datetime']);
    $endTime = new DateTime($info['datetime']);
    date_add($endTime, date_interval_create_from_date_string('1800 seconds'));
    $startTime = $startTime->format('Y-m-d H:i');
    $endTime = $endTime->format('Y-m-d H:i');

    // Create the appointment with this new customer id
    Appointment::addAppointment(Session::get('userID'));

    if ($overlapDays) {
    } else {
      // Remove all dates conflicting with the appointment duration
      BookingDateTime::timeBetween($startTime, $endTime)->delete();
    }

    return View::make('success');
  }

  /**
  * Function to retrieve times available for a given date
  *
  * View is returned in JSON format
  *
  **/
  public function getTimes()
  {


    $selectedDay = Input::get('selectedDay');
    $availableTimes = BookingDateTime::where('userID', Session::get('userID'))->get();

    // For each available time...
    foreach($availableTimes as $t => $value) {

      $startTime = new DateTime($value->booking_datetime);
      if ($startTime->format("Y-m-d") == $selectedDay) {
        $endTime = new DateTime($value->booking_datetime);
        date_add($endTime, date_interval_create_from_date_string('3600 seconds'));

        // Try to grab any appointments between the start time and end time
        $result = Appointment::timeBetween($startTime->format("Y-m-d H:i"), $endTime->format("Y-m-d H:i"));

        // If no records are returned, the time is okay, if not, we must remove it from the array
        if($result->first()) {
          unset($availableTimes[$t]);
        }

      } else {
        unset($availableTimes[$t]);
      }
    }

    return response()->json($availableTimes);
  }
}
