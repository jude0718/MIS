<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualReportYearList;
use App\Models\FileArchive;
use Illuminate\Http\Request;

class AnnualReportController extends Controller
{
    public function index(){
        $main_title = 'Annual Report';
        $nav = 'Dashboard';
        $years = $this->annualReportYearList();
        return view('admin.reports.annual_reports', compact('main_title', 'nav', 'years'));
    }

    public function annualReportYearList() {
        $data = AnnualReportYearList::get();
        return $data;
    }

    public function generateYear(Request $request){
        $yearSetting = AnnualReportYearList::orderBy('year', 'desc')->first();
        if ($yearSetting) {
            $newYear = $yearSetting->year + 1;
        } else {
            $newYear = now()->year;
        }

        // Save the new year to the database
        AnnualReportYearList::create(['year' => $newYear]);

        return response()->json([
            'message' => 'New year generated successfully',
            'year' => $newYear,
        ], 200);

    }

    public function fetchAnnualReportData(){
        $response = [];
        $data = FileArchive::where('module_id', 10)->orderBy('created_at', 'desc')->get();
        foreach($data as $key=>$item){
            $actions = $this->action($item);
            $response[] = [
                'no' => ++$key,
                'filename' => $item->filename,
                'report_type' => $item->module_dtls->module,
                'uploaded_at' => $item->created_at->format('M d, Y'),
                'year' => $item->created_at->format('Y'),
                'action' => $actions['button'],
                'created_by' => $item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname
            ];
        }
        return response()->json($response);
    }

    public function action($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="view-file-btn" data-name="'.$data->filename.'"><i class="bi bi-eye"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-sp-file-btn" data-id="'.$data->id.'" data-name="'.$data->filename.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function deleteSpFile($fileName){
        $filePath = public_path('reports/' . $fileName);
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                $deleted = FileArchive::where('filename', $fileName)->delete();

                if ($deleted) {
                    return response()->json(['success' => true, 'message' => 'File and record deleted successfully.']);
                } else {
                    return response()->json(['success' => false, 'message' => 'File deleted but could not delete the record from the database.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete the file.']);
            }
        }
        return response()->json(['success' => false, 'message' => 'File not found.']);
    }
}
