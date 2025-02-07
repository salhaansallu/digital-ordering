<?php

namespace App\Http\Controllers;

use App\Models\tables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TablesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $id = tables::orderBy('id', 'DESC')->first();

            if ($id == null) {
                $id = 1;
            } else {
                $id = $id->id + 1;
            }

            $table = new tables();
            $table->id = $id;
            $table->status = 'active';

            if ($table->save()) {
                return response(json_encode(['error' => 0, 'msg' => 'Table added successfully']));
            }

            return response(json_encode(['error' => 1, 'msg' => 'Error while creating table']));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(tables $tables)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tables $tables)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tables $tables)
    {
        if (Auth::check()) {
            $id = sanitize($request->input('id'));
            $change = tables::where('id', $id);

            $data = $change->get();
            if ($data->count() > 0) {
                $change = $change->update([
                    "status" => $data[0]->status == 'active' ? 'unactive' : 'active',
                ]);

                if ($change) {
                    return response(json_encode(['error' => 0, 'msg' => 'Table updates successfully']));
                }
            }

            return response(json_encode(['error' => 1, 'msg' => 'Error updating table']));
        }

        return '';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, tables $tables)
    {
        if (Auth::check()) {
            $id = sanitize($request->input('id'));
            $delete = tables::where('id', $id)->delete();
            if ($delete) {
                return response(json_encode(['error' => 0, 'msg' => 'Table deleted successfully']));
            }

            return response(json_encode(['error' => 1, 'msg' => 'Error deleting table']));
        }

        return '';
    }
}
