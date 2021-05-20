<?php

namespace App\Http\Controllers;
use App\Models\Assortment;

use App\Models\AssortmentGroup;
use App\Models\MeasurementUnit;
use App\Models\Unit;
use Illuminate\Http\Request;


class AssortmentController extends Controller
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
    public function getInfo(Request $request) {
        try {
            $unit = Unit::all();
            $measure_unit = MeasurementUnit::all();
            $assortment_group = AssortmentGroup::where('is_main_group', '=', 1)->get();
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => ['unitList' => $unit, 'measureUnitList' => $measure_unit, 'assortmentGroup' => $assortment_group]
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
    public function get(Request $request) {
        try {
            $id = $request->input('id');
            $data = Assortment::find($id);
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
            Assortment::create($request->data);
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

            Assortment::find($request->id)->update($request->data);

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
            $columns = ["assortments.id", "assortments.name", "assortments.index", "assortments.gtin", "unit_name", "measure_unit_name", "assortments.active", "assortments.to_order"];
            $sort_option = $request->sort_option;
            $sort_column = $sort_option['sortBy'];
            $sort_order = $sort_option['sortOrder'];
            $count = $request['count'];
            $page = $request['page'];
            $search_name = $request->search_option['name'];
            $search_index = $request->search_option['index'];
            $search_gtin = $request->search_option['gtin'];
            $search_unit = $request->search_option['unit'];
            $search_measure_unit = $request->search_option['measure_unit'];
            $search_active = $request->search_option['active'];
            $search_to_order = $request->search_option['to_order'];

            $query = Assortment::where('assortments.name', 'LIKE', "%{$search_name}%")
                ->where('assortments.index', 'LIKE', "%{$search_index}%")
                ->where('assortments.gtin', 'LIKE', "%{$search_gtin}%")
                ->leftJoin('units', 'assortments.unit', '=', 'units.id')
                ->leftJoin('measurement_units', 'assortments.measure_unit', '=', 'measurement_units.id')
                ->leftJoin('assortment_groups', 'assortments.assortment_group', '=', 'assortment_groups.id');

            if (intval($search_unit) != 0) {
                $query->where('unit', '=', intval($search_unit));
            }
            if (intval($search_measure_unit) != 0) {
                $query->where('measure_unit', '=', intval($search_measure_unit));
            }
            if (intval($search_active) != 0) {
                $query->where('active', '=', intval($search_active) - 1);
            }
            if (intval($search_to_order) != 0) {
                $query->where('to_order', '=', intval($search_to_order) - 1);
            }

            $total_count = $query->get();

            $list = $query
                ->groupBy('assortments.id')
                ->orderBy($columns[$sort_column], $sort_order)
                ->skip(($page - 1) * $count)
                ->take($count)
                ->selectRaw('assortments.*, units.name as unit_name, measurement_units.name as measure_unit_name, assortment_groups.name as assortment_group_name')
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
            Assortment::where('id', '=', $id)->delete();

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
