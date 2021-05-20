<?php

namespace App\Http\Controllers;
use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\OrkTeam;
use App\Models\QualificationPoint;
use App\Models\QualificationPointType;
use App\Models\RehabitationCenter;
use App\Models\ServiceList;
use App\Models\Training;
use App\Models\TrainingClass;
use App\Models\TrainingComment;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Verify the registered account.
     *
     * @param  Request  $request
     * @return Response
     */
    public function get(Request $request) {
        try {
            $id = $request->input('id');
            $data = Contractor::find($id);
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

    /**
     * Verify the registered account.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request) {
        try {
            Contractor::create($request->data);
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => CREATE_CONTRACTOR_SUCCESS,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

    /**
     * Verify the registered account.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request) {
        try {

            Contractor::find($request->id)->update($request->data);

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => UPDATE_CONTRACTOR_SUCCESS,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

    public function getFilterList(Request $request) {
        try {
            $columns = ["id", "name", "code", "GLN", "supplier", "recipient", "active"];
            $sort_option = $request->sort_option;
            $sort_column = $sort_option['sortBy'];
            $sort_order = $sort_option['sortOrder'];
            $count = $request['count'];
            $page = $request['page'];
            $search_name = $request->search_option['name'];
            $search_code = $request->search_option['code'];
            $search_GLN = $request->search_option['GLN'];
            $search_supplier = $request->search_option['supplier'];
            $search_recipient = $request->search_option['recipient'];
            $search_active = $request->search_option['active'];

            $query = Contractor::where('name', 'LIKE', "%{$search_name}%")
                ->where('code', 'LIKE', "%{$search_code}%")
                ->where('GLN', 'LIKE', "%{$search_GLN}%");

            if (intval($search_active) != 0) {
                $query->where('active', '=', intval($search_active) - 1);
            }

            if (intval($search_supplier) != 0) {
                $query->where('supplier', '=', intval($search_supplier) - 1);
            }

            if (intval($search_recipient) != 0) {
                $query->where('supplier', '=', intval($search_recipient) - 1);
            }

            $total_count = $query->get();

            $list = $query
                ->groupBy('id')
                ->orderBy($columns[$sort_column], $sort_order)
                ->skip(($page - 1) * $count)
                ->take($count)
                ->get();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => [ 'list' => $list, 'count' => count($total_count) ]
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

    public function delete(Request $request) {
        try {
            $id = $request->input('id');
            Contractor::where('id', '=', $id)->delete();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => DELETE_CONTRACTOR_SUCCESS,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

}
