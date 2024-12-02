<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtensionActivity;
use App\Models\Helper;
use App\Models\Research;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtensionAndResearchController extends Controller
{
    public function index(){
        $main_title = 'Research and Extension';
        $nav = 'Dashboard';
        return view('admin.extension_and_research.extension_and_research', compact('main_title', 'nav'));
    }

    public function storeUniversityResearch(Request $request) {
        try {
            $validatedData = $request->validate([
                'title' => 'required',
                'researcher' => 'required',
                'status' => 'required',
                'year' => 'required',
                'budget' => 'required|integer',
                'agency' => 'nullable',

            ]);
    
            try {
                $validatedData['module'] = 5;
                $validatedData['added_by'] = auth()->user()->id;
                Research::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Research and Extension List of on-going and completed faculty researches funded by the University',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Research and Extension List of on-going and completed faculty researches funded by the University',
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

    public function fetchUniversityResearch(){
        $response = [];
        $data = Research::get();
        foreach ($data as $key=>$item) {
            $actions = $this->UniversityResearchaction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'title' => ucwords($item->title),
                'status_details' =>  date('Y', strtotime($item->year)).' / P'.$item->budget.'<br>'.ucwords($item->status),
                'researcher' => ucwords($item->researcher),
                'year' => date('Y', strtotime($item->year)),
                'agency' => empty($item->agency) ? 'CvSU' : ucwords($item->agency),
                'budget' => $item->budget,
                'action' => $actions['button']
            ];
        }
        return response()->json($response); 
    }

    public function UniversityResearchaction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-university-research-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-university-research-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewUniversityResearch($id){
        $data = Research::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateUniversityResearch(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'title' => 'required',
                'researcher' => 'required',
                'status' => 'required',
                'year' => 'required',
                'budget' => 'required|integer',
                'agency' => 'nullable',

            ]);
    
            try {
                Research::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Research and Extension List of on-going and completed faculty researches funded by the University',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Research and Extension List of on-going and completed faculty researches funded by the University',
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

    public function removeUniversityResearch($id){
        $data = Research::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Research and Extension List of on-going and completed faculty researches funded by the University',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Research and Extension List of on-going and completed faculty researches funded by the University',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    public function storeExtensionActivity(Request $request) {
        try {
            $validatedData = $request->validate([
                'extension_activity' => 'required',
                'extensionist' => 'nullable',
                'number_of_beneficiaries' => 'nullable|integer',
                'partner_agency' => 'nullable',
                'activity_date' => 'required'

            ]);
    
            try {
                $validatedData['module'] = 2;
                $validatedData['added_by'] = auth()->user()->id;
                ExtensionActivity::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Research and Extension List of extension activities conducted',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Research and Extension List of extension activities conducted',
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

    public function fetchExtensionActivity(){
        $response = [];
        $data = ExtensionActivity::get();
        foreach ($data as $key=>$item) {
            $actions = $this->ExtensionActivityaction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'extension_activity' => ucwords($item->extension_activity).'<br>'.date('M, d Y', strtotime($item->activity_date)),
                'extensionist' => ucwords($item->extensionist),
                'number_of_beneficiaries' => $item->number_of_beneficiaries,
                'partner_agency' => ucwords($item->partner_agency),
                
                'action' => $actions['button']
            ];
        }
        return response()->json($response); 
    }

    public function ExtensionActivityaction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-extension-activity-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-extension-activity-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewExtensionActivity($id){
        $data = ExtensionActivity::where('id', $id)->first();

        return response()->json([
            'extension_activity' => ucwords($data->extension_activity),
            'activity_date' => date('Y-m-d', strtotime($data->activity_date)),
            'extensionist' =>  ucwords($data->extensionist),
            'number_of_beneficiaries' => $data->number_of_beneficiaries,
            'partner_agency' =>  ucwords($data->partner_agency),

        ]);
    }

    public function updateExtensionActivity(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'extension_activity' => 'required',
                'extensionist' => 'nullable',
                'number_of_beneficiaries' => 'nullable|integer',
                'partner_agency' => 'nullable',
                'activity_date' => 'required'

            ]);
    
            try {
                ExtensionActivity::where('id', $request->id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Research and Extension List of extension activities conducted',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Research and Extension List of extension activities conducted',
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

    public function removeExtensionActivity($id){
        $data = ExtensionActivity::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Research and Extension List of extension activities conducted',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Research and Extension List of extension activities conducted',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}   
