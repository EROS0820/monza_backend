<?php

namespace App\Http\Controllers;
use App\Models\Assortment;

use App\Models\AssortmentGroup;
use App\Models\Contractor;
use App\Models\MeasurementUnit;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseOperation;
use Illuminate\Http\Request;


class WarehouseOperationController extends Controller
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
            $assortment = Assortment::all();
            $warehouse = Warehouse::all();
            $measure_unit = MeasurementUnit::all();
            $contractor = Contractor::all();
            $assortment_group = AssortmentGroup::all();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => ['unit' => $unit, 'assortment' => $assortment, 'assortment_group' => $assortment_group, 'warehouse' => $warehouse, 'measure_unit' => $measure_unit, 'contractor' => $contractor]
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
            $data = WarehouseOperation::find($id);
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
            WarehouseOperation::create($request->data);
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => CREATE_WAREHOUSE_OPERATION_SUCCESS,
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
    public function createList(Request $request) {
        try {
            $rows = $request->data;
            foreach($rows as $item) {
                $group_id = 0;
                $assortment_id = 0;
                $unit_id = 0;
                $measure_unit_id = 0;
                $warehouse_id = 0;
                $contractor_id = 0;
                $assortments = Assortment::where('name', '=', $item['assortment'])->get();
                $units = Unit::where('name', '=', $item['unit'])->get();
                $measure_units = MeasurementUnit::where('name', '=', $item['measure_unit'])->get();
                $warehouses = Warehouse::where('name', '=', $item['warehouse'])->get();
                $contractors = Contractor::where('name', '=', $item['contractor'])->get();
                if (count($assortments) === 0 || count($units) === 0 || count($measure_units) === 0 || count($warehouses) === 0 || count($contractors) === 0) {
                    continue;
                }
                $assortment_id = $assortments[0]->id;
                $unit_id = $units[0]->id;
                $measure_unit_id = $measure_units[0]->id;
                $warehouse_id = $warehouses[0]->id;
                $contractor_id = $contractors[0]->id;

                WarehouseOperation::create([
                    'assortment' => $assortment_id,
                    'unit' => $unit_id,
                    'measure_unit' => $measure_unit_id,
                    'warehouse' => $warehouse_id,
                    'contractor' => $contractor_id,
                    'date' => $item['date'],
                    'receipt_value' => $this->fixNumberType($item['receipt_value']),
                    'issue_amount' => $this->fixNumberType($item['issue_amount']),
                    'reception_frequency' => $this->fixNumberType($item['reception_frequency']),
                    'release_frequency' => $this->fixNumberType($item['release_frequency']),
                    'inventory' => $this->fixNumberType($item['inventory']),
                    'order_quantity' => $this->fixNumberType($item['order_quantity']),
                ]);
            }

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => IMPORT_SUCCESS,
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

            WarehouseOperation::find($request->id)->update($request->data);

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => UPDATE_WAREHOUSE_OPERATION_SUCCESS,
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
            $columns = ["warehouse_operations.id", "warehouse_operations.date", "warehouse_operations.assortment", "assortment_group", "warehouse_operations.unit",
                "warehouse_operations.measure_unit", "warehouse_operations.contractor", "warehouse_operations.warehouse", "warehouse_operations.receipt_value", "warehouse_operations.issue_amount"];
            $sort_option = $request->sort_option;
            $sort_column = $sort_option['sortBy'];
            $sort_order = $sort_option['sortOrder'];
            $count = $request['count'];
            $page = $request['page'];
            $search_start_date = $request->search_option['start_date'];
            $search_end_date = $request->search_option['end_date'];
            $search_assortment = $request->search_option['assortment'];
            $search_assortment_group = $request->search_option['assortment_group'];
            $search_unit = $request->search_option['unit'];
            $search_measure_unit = $request->search_option['measure_unit'];
            $search_contractor = $request->search_option['contractor'];
            $search_warehouse = $request->search_option['warehouse'];
            $search_receipt_value = $request->search_option['receipt_value'];
            $search_issue_amount = $request->search_option['issue_amount'];

            $query = WarehouseOperation
                ::where('date', '>=', $search_start_date)
                ->where('date', '<=', $search_end_date)
                ->where('receipt_value', 'LIKE', "%{$search_receipt_value}%")
                ->where('issue_amount', 'LIKE', "%{$search_issue_amount}%")
                ->leftJoin('assortments', 'assortments.id', '=', 'warehouse_operations.assortment')
            ;

            if (intval($search_assortment) != 0) {
                $query->where('warehouse_operations.assortment', '=', intval($search_assortment));
            }
            if (intval($search_assortment_group) != 0) {
                $query->where('assortments.assortment_group', '=', intval($search_assortment_group));
            }
            if (intval($search_unit) != 0) {
                $query->where('warehouse_operations.unit', '=', intval($search_unit));
            }
            if (intval($search_measure_unit) != 0) {
                $query->where('warehouse_operations.measure_unit', '=', intval($search_measure_unit));
            }
            if (intval($search_contractor) != 0) {
                $query->where('warehouse_operations.contractor', '=', intval($search_contractor));
            }
            if (intval($search_warehouse) != 0) {
                $query->where('warehouse_operations.warehouse', '=', intval($search_warehouse));
            }

            $total_count = $query->get();

            $list = $query
                ->groupBy('warehouse_operations.id')
                ->orderBy($columns[$sort_column], $sort_order)
                ->skip(($page - 1) * $count)
                ->take($count)
                ->selectRaw('warehouse_operations.*')
                ->get();

            foreach($list as $item) {
                $assortment_item = $item->assortment_item()->get();
                $unit_item = $item->unit_item()->get();
                $measure_unit_item = $item->measure_unit_item()->get();
                $warehouse_item = $item->warehouse_item()->get();
                $contractor_item = $item->contractor_item()->get();

                if (count($assortment_item) != 0) {
                    $item['assortment_name'] =  $assortment_item[0]->name;
                    $assortment_group_item = $assortment_item[0]->assortment_group_item()->get();
                    if (count($assortment_group_item) != 0) {
                        $item['assortment_group_name'] = $assortment_group_item[0]->name;
                    }
                }

                if (count($unit_item) != 0) {
                    $item['unit_name'] =  $unit_item[0]->name;
                }

                if (count($measure_unit_item) != 0) {
                    $item['measure_unit_name'] = $measure_unit_item[0]->name;
                }

                if (count($warehouse_item) != 0) {
                    $item['warehouse_name'] = $warehouse_item[0]->name;
                }

                if (count($contractor_item) != 0) {
                    $item['contractor_name'] = $contractor_item[0]->name;
                }
            }

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
            WarehouseOperation::where('id', '=', $id)->delete();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => DELETE_WAREHOUSE_OPERATION_SUCCESS,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

}
