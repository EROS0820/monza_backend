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
            $assortment_group = AssortmentGroup::all();
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
        public function export(Request $request) {
        try {
            $list = Assortment
                ::leftJoin('units', 'assortments.unit', '=', 'units.id')
                ->leftJoin('measurement_units', 'assortments.measure_unit', '=', 'measurement_units.id')
                ->leftJoin('assortment_groups', 'assortments.assortment_group', '=', 'assortment_groups.id')
                ->groupBy('assortments.id')
                ->selectRaw('assortments.*, units.name as unit_name, measurement_units.name as measure_unit_name, assortment_groups.name as assortment_group_name')
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
            Assortment::create($request->data);
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
                $measure_unit = MeasurementUnit::where('name', '=', $item['measure_unit_name'])->get();
                $unit = Unit::where('name', '=', $item['unit_name'])->get();
                $assortment_group = AssortmentGroup::where('name', '=', $item['assortment_group_name'])->get();

                if ($item['measure_unit_name'] != null) {
                    if (count($measure_unit) == 0) {
                        $measure_unit_item = new MeasurementUnit();
                        $measure_unit_item->name = $item['measure_unit_name'];
                        $measure_unit_item->description = '';
                        $measure_unit_item->save();
                        $measure_unit_id = $measure_unit_item->id;
                    } else {
                        $measure_unit_id = $measure_unit[0]->id;
                    }
                }

                if ($item['unit_name'] != null) {
                    if (count($unit) == 0) {
                        $unit_item = new Unit();
                        $unit_item->name = $item['unit_name'];
                        $unit_item->save();
                        $unit_id = $unit_item->id;
                    } else {
                        $unit_id = $unit[0]->id;
                    }
                }

                if ($item['assortment_group_name'] != null) {
                    if (count($assortment_group) == 0) {
                        $assortment_group_item = new AssortmentGroup();
                        $assortment_group_item->name = $item['assortment_group_name'];
                        $assortment_group_item->is_main_group = true;
                        $assortment_group_item->save();
                        $assortment_group_id = $assortment_group_item->id;
                    } else {
                        $assortment_group_id = $assortment_group[0]->id;
                    }
                }
                $is_exist = (Assortment::where('name', '=', $item['name'])->count() != 0);
                if ($is_exist) {
                    Assortment::where('name', '=', $item['name'])->update([
                        'name' => $item['name'],
                        'index' => $item['index'],
                        'gtin' => $item['gtin'],
                        'unit' => $unit_id,
                        'measure_unit' => $measure_unit_id,
                        'active' => ($item['active'] == 'TAK'),
                        'to_order' => ($item['to_order'] == 'TAK'),
                        'purchase_price' => $this->fixNumberType($item['purchase_price']),
                        'sale_price' => $this->fixNumberType($item['sale_price']),
                        'assortment_group' => $assortment_group_id,
                        'assortment_type' => $item['assortment_type'],
                        'service_demand' => $this->fixNumberType($item['service_demand']),
                        'refill_cycle_time' => $this->fixNumberType($item['refill_cycle_time']),
                        'cycle_time_deviations' => $this->fixNumberType($item['cycle_time_deviations']),
                        'inventory_cost_factor' => $this->fixNumberType($item['inventory_cost_factor'])
                    ]);
                } else {
                    $assortment = new Assortment();
                    $assortment->name = $item['name'];
                    $assortment->index = $item['index'];
                    $assortment->gtin = $item['gtin'];
                    $assortment->unit = $unit_id;
                    $assortment->measure_unit = $measure_unit_id;
                    $assortment->active = ($item['active'] == 'TAK');
                    $assortment->to_order = ($item['to_order'] == 'TAK');
                    $assortment->purchase_price = $this->fixNumberType($item['purchase_price']);
                    $assortment->sale_price = $this->fixNumberType($item['sale_price']);
                    $assortment->assortment_group = $assortment_group_id;
                    $assortment->assortment_type = $item['assortment_type'];
                    $assortment->service_demand = $this->fixNumberType($item['service_demand']);
                    $assortment->refill_cycle_time = $this->fixNumberType($item['refill_cycle_time']);
                    $assortment->cycle_time_deviations = $this->fixNumberType($item['cycle_time_deviations']);
                    $assortment->inventory_cost_factor = $this->fixNumberType($item['inventory_cost_factor']);
                    $assortment->save();
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

            Assortment::find($request->id)->update($request->data);

            return response()->json([
                'code' => SUCCESS_CODE,
                'message' => UPDATE_ASSORTMENT_SUCCESS,
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
                'message' => DELETE_ASSORTMENT_SUCCESS,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'code' => SERVER_ERROR_CODE,
                'message' => SERVER_ERROR_MESSAGE
            ]);
        }
    }

}
