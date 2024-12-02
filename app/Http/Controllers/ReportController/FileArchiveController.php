<?php

namespace App\Http\Controllers\ReportController;
use App\Http\Controllers\Controller;
use App\Models\AccreditationStatus;
use App\Models\EducationalAttainment;
use App\Models\Enrollment;
use App\Models\EventsAndAccomplishments;
use App\Models\FileArchive;
use App\Models\InfrastructureDevelopment;
use App\Models\Linkages;
use App\Models\ModuleHeader;
use App\Models\Research;
use App\Models\StudentOrganizations;
use Illuminate\Http\Request;

class FileArchiveController extends Controller
{
    public function index(){
        $main_title = 'File Archive'; 
        $nav = 'Dashboard';
        $modules = $this->ModuleList();
        return view('admin.reports.file_archives', compact('main_title', 'nav', 'modules'));
    }

    public function getYearPerModule(Request $request) {
        $module_id = $request->module;
        $data_query = '';
    
        switch ($module_id) {
            case 1:
                $data_query = AccreditationStatus::selectRaw('YEAR(created_at) as year');
                break;
            case 2:
                $data_query = Enrollment::selectRaw('YEAR(created_at) as year');
                break;
            case 3:
                $data_query = EducationalAttainment::selectRaw('YEAR(created_at) as year');
                break;
            case 4:
                $data_query = StudentOrganizations::selectRaw('YEAR(created_at) as year');
                break;
            case 5:
                $data_query = Research::selectRaw('YEAR(created_at) as year');
                break;
            case 6:
                $data_query = Linkages::selectRaw('YEAR(created_at) as year');
                break;
            case 7:
                $data_query = InfrastructureDevelopment::selectRaw('YEAR(created_at) as year');
                break;
            case 8:
                $data_query = EventsAndAccomplishments::selectRaw('YEAR(created_at) as year');
                break;
            default:
                $data_query = AccreditationStatus::selectRaw('YEAR(created_at) as year');
                break;
        }
        
        return response()->json($this->getModulePerYear($data_query));
    }
    

    public function getModulePerYear($query) {
        $data = $query
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();
        return $data;
    }

    public function fetchReportData(){
        $response = [];
        $data = FileArchive::get();
        foreach($data as $key=>$item){
            $actions = $this->action($item);
            $response[] = [
                'no' => ++$key,
                'filename' => $item->filename,
                'report_type' => $item->module_dtls->module,
                'uploaded_at' => $item->created_at->format('M d, Y'),
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

    public function ModuleList(){
        $data = ModuleHeader::where('id', '!=', 10)->get();

        return $data;
    }

    
}
