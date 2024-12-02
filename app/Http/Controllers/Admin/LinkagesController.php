<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Linkages;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkagesController extends Controller
{
    public function index(){
        $main_title = 'Linkages';
        $nav = 'Dashboard';
      
        return view('admin.linkages.linkages', compact('main_title', 'nav'));
    }

    public function storeLinkages(Request $request) {
        try {
            $validatedData = $request->validate([
                'agency' => 'required',
                'linkage_nature' => 'required',
                'activity_title' => 'required',
                'date' => 'required',
                'date' => 'required',
                'venue' => 'required',
                'attendees' => 'required',
                'facilitators' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 7;
                $validatedData['added_by'] = auth()->user()->id;
                Linkages::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Linkages ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Linkages ',
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

    public function fetchLinkages(){
        $response = [];
        $data = Linkages::get();
        foreach ($data as $key=>$item) {
            $actions = $this->Linkagesaction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'agency' => ucwords($item->agency),
                'linkage_nature' => ucwords($item->linkage_nature),
                'activity_title' => ucwords($item->activity_title),
                'date_venue' =>  date('M d, Y', strtotime($item->date)).'/'.ucwords($item->venue),
                'attendees' => ucwords($item->attendees),
                'facilitators' => ucwords($item->facilitators),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function Linkagesaction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-linkages-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-linkages-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewLinkages($id){
        $data = Linkages::where('id', $id)->first();

        return response()->json([
            'agency' => $data->agency,
            'linkage_nature' =>$data->linkage_nature,
            'activity_title' =>$data->activity_title,
            'date' =>  date('Y-m-d', strtotime($data->date)),
            'venue' => $data->venue,
            'attendees' => $data->attendees,
            'facilitators' => $data->facilitators,
        ]);
    }

    public function updateLinkages(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'agency' => 'required',
                'linkage_nature' => 'required',
                'activity_title' => 'required',
                'date' => 'required',
                'date' => 'required',
                'venue' => 'required',
                'attendees' => 'required',
                'facilitators' => 'required',
            ]);
    
            try {
                Linkages::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Linkages ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Linkages ',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
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

    public function removeLinkages($id){
        $data = Linkages::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Linkages ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Linkages ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}
