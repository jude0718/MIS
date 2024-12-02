<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AwardsDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\AwardsHeader;
use App\Models\Helper;
use App\Models\Programs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecognitionAndAwardController extends Controller
{
    public function index(){
        $main_title = 'Recognition and Award';
        $nav = 'Student Profile';
        $programs = $this->programList();
        return view('admin.student_profile.recognition_and_award', compact('main_title', 'nav', 'programs'));
    }

    public function programList(){
        $data = Programs::get();

        return $data;
    }

    public function storeAward(Request $request) {
        try {
            $validatedData = $request->validate([
                'award' => 'required',
                'granting_agency' => 'required',
                'start_year' => 'required',
                'end_year' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 2;
                $validatedData['created_by'] = auth()->user()->id;
                AwardsHeader::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Recognition and Award ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Recognition and Award ',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchAwardData(){
        $response = [];
        $data = AwardsHeader::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->headerAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'granting_agency' => $item->granting_agency,
                'award' => $item->award,
                'year' => date('Y', strtotime($item->start_year)).'-'.date('Y', strtotime($item->end_year)),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function headerAction($data){
        $button = '
            <button type="button" class="btn btn-outline-warning btn-sm px-3" id="view-modal-hdr-btn" data-id="'.$data->id.'"><i class="bi bi-eye"></i></button>
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-modal-hdr-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-award-header-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewAwardHeaderData($id){
        $data = AwardsHeader::where('id', $id)->first();
    
        return response()->json([
            'created_by' => $data->created_by_dtls->firstname.' '.$data->created_by_dtls->lastname,
            'award' => $data->award,
            'granting_agency' => $data->granting_agency,
            'start_year' => date('Y-m-d', strtotime($data->start_year)),
            'end_year' => date('Y-m-d', strtotime($data->end_year)),
        ]);
    }

    public function updateAwardHeader(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'award' => 'required',
                'granting_agency' => 'required',
                'start_year' => 'required',
                'end_year' => 'required',
            ]);
    
            try {
                $validatedData['created_by'] = auth()->user()->id;
                AwardsHeader::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Recognition and Award ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Recognition and Award ',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error updating the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeAward($id){
        $data = AwardsHeader::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Recognition and Award ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Recognition and Award ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    public function storeAwardDetails(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'game_placement' => 'required',
                'grantees_name' => 'required',
                'award_details' => 'required',
                'medal_type' => 'required',
                'program_id' => 'required',
            ]);
    
            try {
                $validatedData['created_by'] = auth()->user()->id;
                $validatedData['awards_hdr'] = $id;
                AwardsDetails::create($validatedData);
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchAwardDetailsData($id){
        $response = [];
        $data = AwardsHeader::where('id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($data as $value) {
            foreach($value->award_dtls as $key=>$item){
                $actions = $this->detailsAction($item);
                $response[] = [
                    'no' => ++$key,
                    'grantees_name' => $item->grantees_name,
                    'game_placement' => $item->game_placement,
                    'award_details' => $item->award_details,
                    'action' => $actions['button']
                ];
            }
        }
        return response()->json($response);
    }

    public function detailsAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-modal-dtls-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-award-dtls-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewAwardDetailsData($id){
        $data = AwardsDetails::where('id', $id)->first();
    
        return response()->json($data);
    }

    public function updateAwardDetails(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'game_placement' => 'required',
                'grantees_name' => 'required',
                'award_details' => 'required',
                'medal_type' => 'required',
                'program_id' => 'required',
            ]);
    
            try {
                AwardsDetails::where('id', $id)->update($validatedData);
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error updating the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeAwardDetails($id){
        $data = AwardsDetails::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Recognition and Award ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Recognition and Award ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}
