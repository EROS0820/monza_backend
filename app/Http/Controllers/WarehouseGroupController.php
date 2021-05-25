<?php

namespace App\Http\Controllers;
use App\Models\Candidate;
use App\Models\OrkTeam;
use App\Models\QualificationPoint;
use App\Models\QualificationPointType;
use App\Models\RehabitationCenter;
use App\Models\ServiceList;
use App\Models\Training;
use App\Models\TrainingClass;
use App\Models\TrainingComment;
use App\Models\Warehouse;
use App\Models\WarehouseGroup;
use App\Models\WarehouseGroupRelation;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseGroupController extends Controller
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
            $data = Warehouse::all();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => ['warehouse_list' => $data]
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
            $data = WarehouseGroup::find($id);
            $_temp = [];
            $warehouses = WarehouseGroupRelation::where('warehouse_group_id', '=', $id)->get();
            foreach($warehouses as $item) {
                $_temp[] = $item->warehouse_id;
            }
            $data['warehouses'] = $_temp;

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
    public function export(Request $request) {
        try {
            $list = WarehouseGroup::all();
            foreach($list as $item) {
                $warehouses = [];
                $relations = $item->getWarehouseGroupRelation()->get();
                foreach($relations as $relation) {
                    $warehouses[] = $relation->getWarehouse()->first()->name;
                }
                $item['warehouses'] = implode(',', $warehouses);
            }

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => $list
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
            $warehouses = $request->data['warehouses'];
            $warehouse_group = new WarehouseGroup($request->data);
            $warehouse_group->save();

            foreach($warehouses as $item) {
                $warehouse = new WarehouseGroupRelation();
                $warehouse->warehouse_group_id = $warehouse_group->id;
                $warehouse->warehouse_id = $item;
                $warehouse->save();
            }

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => CREATE_WAREHOUSE_GROUP_SUCCESS,
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
                $warehousegroup = WarehouseGroup::where('name', '=', $item['name'])->count();
                $warehouses = explode(",", $item['warehouses']);
                $id = 0;
                if ($warehousegroup === 0) {
                    $to_save = new WarehouseGroup();
                    $to_save->name = $item['name'];
                    $to_save->active = ($item['active'] === 'TAK');
                    $to_save->description = $item['description'];
                    $to_save->received = $item['received'];
                    $to_save->releases = $item['releases'];
                    $to_save->supply = $item['supply'];
                    $to_save->save();
                    $id = $to_save->id;
                } else {
                    WarehouseGroup::where('name', '=', $item['name'])->update([
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'active' => $item['active'] === 'TAK',
                        'received' => $item['received'],
                        'releases' => $item['releases'],
                        'supply' => $item['supply']
                    ]);
                    $id = WarehouseGroup::where('name', '=', $item['name'])->first()->id;
                }
                foreach($warehouses as $item_warehouse) {
                    $count = Warehouse::where('name', '=', $item_warehouse)->count();
                    $warehouse_id = 0;
                    if ($count === 0) {
                        $warehouse = new Warehouse();
                        $warehouse->name = $item_warehouse;
                        $warehouse->save();
                        $warehouse_id = $warehouse->id;
                    } else {
                        $warehouse_id = Warehouse::where('name', '=', $item_warehouse)->first()->id;
                    }
                    if (WarehouseGroupRelation::where('warehouse_group_id', '=', $id)->where('warehouse_id', '=', $warehouse_id)->count() == 0) {
                        $relation = new WarehouseGroupRelation();
                        $relation->warehouse_group_id = $id;
                        $relation->warehouse_id = $warehouse_id;
                        $relation->save();
                    }

                }
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
            $id = $request->id;
            $warehouses = $request->data['warehouses'];

            WarehouseGroup::find($id)->update($request->data);

            WarehouseGroupRelation::where('warehouse_group_id', '=', $id)->delete();

            foreach($warehouses as $item) {
                $warehouse = new WarehouseGroupRelation();
                $warehouse->warehouse_group_id = $id;
                $warehouse->warehouse_id = $item;
                $warehouse->save();
            }

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => UPDATE_WAREHOUSE_GROUP_SUCCESS,
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
            $columns = ["id", "name", "name", "warehouse_groups.active"];
            $sort_option = $request->sort_option;
            $sort_column = $sort_option['sortBy'];
            $sort_order = $sort_option['sortOrder'];
            $count = $request['count'];
            $page = $request['page'];
            $search_name = $request->search_option['name'];
            $search_subname = $request->search_option['sub_name'];
            $search_active = $request['search_option']['active'];

            $query = WarehouseGroup::where('name', 'LIKE', "%{$search_name}%");

            if (intval($search_active) != 0) {
                $query->where('active', '=', intval($search_active) - 1);
            }

            $total_count = $query->get();

            $list = $query
                ->groupBy('id')
                ->orderBy($columns[$sort_column], $sort_order)
                ->skip(($page - 1) * $count)
                ->take($count)
                ->get();

            foreach($list as $item) {
                $sub_list = Warehouse
                    ::leftJoin('warehouse_group_relations', 'warehouse_group_relations.warehouse_id', '=', 'warehouses.id' )
                    ->where('warehouse_group_relations.warehouse_group_id', '=', $item->id)
                    ->where('name', 'LIKE', "%{$search_subname}%")
                    ->orderBy('name', $sort_order)
                    ->get();
                $item['sub_list'] = $sub_list;
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
            WarehouseGroup::where('id', '=', $id)->delete();
            WarehouseGroupRelation::where('warehouse_group_id', '=', $id)->delete();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => DELETE_WAREHOUSE_GROUP_SUCCESS,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

}
