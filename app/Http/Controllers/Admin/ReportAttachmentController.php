<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleHeader;
use App\Models\Modules;
use App\Models\Programs;
use App\Models\ReportAttachmentDetails;
use App\Models\ReportAttachmentHeader;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportAttachmentController extends Controller
{
    public function index(){
        $main_title = 'Report Attachment';
        $nav = 'Dashboard';
        $modules = $this->moduleList();
        return view('admin.report_attachment', compact('main_title', 'nav', 'modules'));
    }

    public function moduleList(){
        $data = ModuleHeader::get();

        return $data;
    }

    public function storeReportAttachment(Request $request){
        try{
            $validatedData = $request->validate([
                'attachment' => 'required|array',
                'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'attachment_detail' => 'required',
                'module_id' => 'required'
            ]);

            if (count($request->file('attachment')) > 4) {
                return response()->json(['errors' => ['attachment' => 'You can upload a maximum of 4 images.']], 422);
            }
            
            $data_hdr = ReportAttachmentHeader::create([
                'added_by' => Auth::id(),
                'module_id' => $request->module_id,
                'attachment_detail' => $request->attachment_detail
            ]);

            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $attachment) {
                    $attachmentName = time() . '_' . uniqid() . '.' . $attachment->getClientOriginalExtension();
                    $attachment->move(public_path('images/report_attachment'), $attachmentName);
    
                    ReportAttachmentDetails::create([
                        'attachment_hdr' => $data_hdr->id,
                        'attachment' => $attachmentName
                    ]);
                }
            }
            
            return response()->json(['message' => 'Attachment Uploaded successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }

    public function fetchAttachment(){
        $response = [];
        $data = ReportAttachmentHeader::get();
        foreach ($data as $key=>$item) {
            $actions = $this->header_action($item);
            $response[] = [
                'no' => ++$key,
                'module_id' => ucwords(strtolower($item->module_dtls->module)),
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'attachment_detail' => $item->attachment_detail,
                'created_at' => $item->created_at->format('M d, Y'),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function header_action($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-attachment-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-warning btn-sm px-3" id="view-attachment-btn" data-id="'.$data->id.'"><i class="bi bi-file-image"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-header-attachment-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }
    
    public function viewHeaderAttachment($id){
        $data = ReportAttachmentHeader::find($id);

        return response()->json($data);
    }

    public function viewAttachment($id){
        $response = [];
        $data = ReportAttachmentHeader::where('id', $id)->get();
        foreach ($data as $item) {
            foreach ($item->attachment_dtls as  $key=>$attachment) {
                $actions = $this->dlts_action($attachment);
                $response[] = [
                    'no' => ++$key,
                    'attachment' => $attachment->attachment,
                    'image' => '<img style="width:50px; height:50px;" src="'.asset('images/report_attachment/'. $attachment->attachment).'">',
                    'action' => $actions['button']
                ];
            }
        }
        return response()->json($response);
    }

    public function dlts_action($data){
        $button = '
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-attachment-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';
        return [
            'button' => $button,
        ];
    }


    public function addAttachment(Request $request, $id){
        try{
            $validatedData = $request->validate([
                'attachment' => 'required|array',
                'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if (count($request->file('attachment')) > 4) {
                return response()->json(['errors' => ['attachment' => 'You can upload a maximum of 4 images.']], 422);
            }
        

            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $attachment) {
                    $attachmentName = time() . '_' . uniqid() . '.' . $attachment->getClientOriginalExtension();
                    $attachment->move(public_path('images/report_attachment'), $attachmentName);
    
                    ReportAttachmentDetails::create([
                        'attachment_hdr' => $id,
                        'attachment' => $attachmentName
                    ]);
                }
            }
            
            return response()->json(['message' => 'Attachment Added successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }

    public function updateReportAttachment(Request $request, $id){
        try{
            $validatedData = $request->validate([
                'attachment_detail' => 'required',
                'module_id' => 'required'
            ]);

          
            $data_hdr = ReportAttachmentHeader::where('id', $id)->update([
                'module_id' => $request->module_id,
                'attachment_detail' => $request->attachment_detail
            ]);

           
            return response()->json(['message' => 'Attachment Updated successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }
    
    public function removeAttachment($id){
        $failedFiles = null;
        $data = ReportAttachmentDetails::find($id);

        $filePath = public_path('images/report_attachment/' . $data->attachment);
            
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                $failedFiles = $data->attachment;
            }
        } else {
            $failedFiles = $data->attachment;
        }
        $isDeleted = $data->delete();

        if (empty($failedFiles)) {
            if ($isDeleted) {
                return response()->json(['success' => true, 'message' => 'files and record deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Files deleted, but could not delete the record from the database.']);
            }
        } else {
            $message = 'Failed to delete the following files: ' .$failedFiles;
            return response()->json(['success' => false, 'message' => $message]);
        }
    }

    public function removeHeaderAttachment($id)  {
        $header = ReportAttachmentHeader::with('attachment_dtls')->find($id);

        if (!$header) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }
        $failedFiles = [];
        foreach ($header->attachment_dtls as $attachment) {
            $filePath = public_path('images/report_attachment/' . $attachment->attachment);
            
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    $failedFiles[] = $attachment->attachment;
                }
            } else {
                $failedFiles[] = $attachment->attachment;
            }
        }

        $header->attachment_dtls()->delete();
        $headerDeleted = $header->delete();

        if (empty($failedFiles)) {
            if ($headerDeleted) {
                return response()->json(['success' => true, 'message' => 'All files and record deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Files deleted, but could not delete the record from the database.']);
            }
        } else {
            $message = 'Failed to delete the following files: ' . implode(', ', $failedFiles);
            return response()->json(['success' => false, 'message' => $message]);
        }
    }


}
