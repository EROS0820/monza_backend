<?php

namespace App\Http\Controllers;
use App\Models\AssortmentGroup;

use Illuminate\Http\Request;


class AssortmentGroupController extends Controller
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
            $data = AssortmentGroup::where('is_main_group', '=', 1)->get();

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => SUCCESS_MESSAGE,
                'data' => ['groupList' => $data]
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
            $data = AssortmentGroup::find($id);
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
            $list = AssortmentGroup
                ::leftJoin('assortment_groups as assortment_main', 'assortment_groups.main_group', '=', 'assortment_main.id')
                ->groupBy('assortment_groups.id')
                ->selectRaw('assortment_groups.*, assortment_main.name as main_group_name')
                ->get();

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
            AssortmentGroup::create($request->data);
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => CREATE_ASSORTMENT_GROUP_SUCCESS,
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
                $is_main_group = ($item['is_main_group'] == 'TAK');

                if (!$is_main_group)
                    continue;

                $assortment_group_count = AssortmentGroup::where('name', '=', $item['name'])->count();

                if ($assortment_group_count == 0) {
                    $assortment_group = new AssortmentGroup();
                    $assortment_group->name = $item['name'];
                    $assortment_group->is_main_group = $is_main_group;
                    $assortment_group->main_group = 0;
                    $assortment_group->code = $item['code'];
                    $assortment_group->description = $item['description'];
                    $assortment_group->service_demand = $this->fixNumberType($item['service_demand']);
                    $assortment_group->refill_cycle_time = $this->fixNumberType($item['refill_cycle_time']);
                    $assortment_group->cycle_time_deviations = $this->fixNumberType($item['cycle_time_deviations']);
                    $assortment_group->inventory_cost_factor = $this->fixNumberType($item['inventory_cost_factor']);
                    $assortment_group->save();
                } else {
                    AssortmentGroup::where('name', '=', $item['name'])->update([
                       'is_main_group' => $is_main_group,
                        'main_group' => 0,
                        'code' => $item['code'],
                        'description' => $item['description'],
                        'service_demand' => $this->fixNumberType($item['service_demand']),
                        'refill_cycle_time' => $this->fixNumberType($item['refill_cycle_time']),
                        'cycle_time_deviations' => $this->fixNumberType($item['cycle_time_deviations']),
                        'inventory_cost_factor' => $this->fixNumberType($item['inventory_cost_factor'])
                    ]);
                }
            }

            foreach($rows as $item) {
                $is_main_group = ($item['is_main_group'] == 'TAK');

                if ($is_main_group)
                    continue;

                $main_group_id = AssortmentGroup::where('name', '=', $item['main_group_name'])->first()->id;
                $assortment_group_count = AssortmentGroup::where('name', '=', $item['name'])->count();

                if ($assortment_group_count == 0) {
                    $assortment_group = new AssortmentGroup();
                    $assortment_group->name = $item['name'];
                    $assortment_group->is_main_group = $is_main_group;
                    $assortment_group->main_group = $main_group_id;
                    $assortment_group->code = $item['code'];
                    $assortment_group->description = $item['description'];
                    $assortment_group->service_demand = $this->fixNumberType($item['service_demand']);
                    $assortment_group->refill_cycle_time = $this->fixNumberType($item['refill_cycle_time']);
                    $assortment_group->cycle_time_deviations = $this->fixNumberType($item['cycle_time_deviations']);
                    $assortment_group->inventory_cost_factor = $this->fixNumberType($item['inventory_cost_factor']);
                    $assortment_group->save();
                } else {
                    AssortmentGroup::where('name', '=', $item['name'])->update([
                        'is_main_group' => $is_main_group,
                        'main_group' => $main_group_id,
                        'code' => $item['code'],
                        'description' => $item['description'],
                        'service_demand' => $this->fixNumberType($item['service_demand']),
                        'refill_cycle_time' => $this->fixNumberType($item['refill_cycle_time']),
                        'cycle_time_deviations' => $this->fixNumberType($item['cycle_time_deviations']),
                        'inventory_cost_factor' => $this->fixNumberType($item['inventory_cost_factor'])
                    ]);
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

            AssortmentGroup::find($request->id)->update($request->data);

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => UPDATE_ASSORTMENT_GROUP_SUCCESS,
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
            $search_code = $request->search_option['code'];

            $query = AssortmentGroup::where('is_main_group', '=', 1)->where('name', 'LIKE', "%{$search_name}%")->where('code', 'LIKE', "%{$search_code}%");

            $total_count = $query->get();

            $list = $query
                ->groupBy('id')
                ->orderBy($columns[$sort_column], $sort_order)
                ->skip(($page - 1) * $count)
                ->take($count)
                ->get();

            foreach($list as $item) {
                $sub_list = AssortmentGroup
                    ::where('is_main_group', '=', 0)
                    ->where('main_group', '=', $item->id)
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
            AssortmentGroup::where('id', '=', $id)->orWhere('main_group', '=', $id)->delete();
            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => DELETE_ASSORTMENT_GROUP_SUCCESS,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

}
