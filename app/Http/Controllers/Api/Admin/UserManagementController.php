<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\UserOnboardingEVent;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Jobs\OnboardUserJob;
use App\Mail\UserUnboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserManagementController extends BaseController
{

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public  function  exportData(){
        $data = DB::table('users')->orderBy('created_at', 'DESC')->get();
        $data_array [] = array("Name","Email","Phone_Number",);
        foreach($data as $data_item)
        {
            $data_array[] = array(
                'Name'          => $data_item->name,
                'Email'         => $data_item->email,
                'Phone_Number'  => $data_item->phone_number,
            );
        }
        $this->bulkUserExport($data_array);
    }


    public function bulkUserExport($user_data){

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($user_data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="User_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }

    }


    /**
     * @param Request $request
//     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
   public  function bulkUserImport(Request $request){

       $validator = Validator::make($request->all(), [
           'file' => 'required|file|mimes:xls,xlsx'
       ]);

       if($validator->fails()){
           return $this->sendError('Error validation', $validator->errors());
       }


       $the_file = $request->file('file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'F', $column_limit );
            $startcount = 2;
            $data = array();
            foreach ( $row_range as $index =>  $row ) {
                if($sheet->getCell( 'A' . $row )->getValue() === null){
                    break;
                }
                $password = Str::random(6);;
                $data[] = [
                    'name' =>$sheet->getCell( 'A' . $row )->getValue(),
                    'email' => $sheet->getCell( 'B' . $row )->getValue(),
                    'phone_number' => $sheet->getCell( 'C' . $row )->getValue(),
                    'password' => Hash::make($password),
                ];

                $startcount++;
                
                dispatch(new OnboardUserJob($data[$index]['name'],$data[$index]['email'], $password));

            }
            DB::table('users')->insert($data);
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];

            return $this->sendError('Oops!!.', ['error'=>'There was a problem uploading the data!']);

        }

        $success['message'] = 'Data successfully uploaded';
       return $this->sendResponse($success,  'Data successfully uploaded');
    }


}
