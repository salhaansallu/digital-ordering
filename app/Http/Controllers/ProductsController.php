<?php

namespace App\Http\Controllers;

use App\Models\favourits;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getProducts()
    {
        $response = [];
        if (Auth::check()) {
            return response(json_encode(Products::all()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function getFavourits()
    {

        if (Auth::check()) {
            $fav = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->get();
            return response(json_encode($fav));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
    }

    public function addFavourits(Request $request)
    {
        if (Auth::check()) {
            $sku = sanitize($request->input('pro_sku'));
            if (empty($sku)) {
                return response(json_encode(array('error' => 1, 'msg' => 'Error while adding to favourits')));
            }
            $verify = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->where('sku', $sku)->get();
            if ($verify->count() == 0) {
                $fav = new favourits();
                $fav->sku = $sku;
                $fav->pos_code = PosDataController::company()->pos_code;
                $fav->user_id = Auth::user()->id;

                if ($fav->save()) {
                    return response(json_encode(array('error' => 0)));
                } else {
                    return response(json_encode(array('error' => 1, 'msg' => 'Error while adding to favourits')));
                }
            }
            return response(json_encode(array('msg' => 'Product already added')));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
    }

    public function removeFavourits(Request $request)
    {
        if (Auth::check()) {
            $sku = sanitize($request->input('pro_sku'));

            if (empty($sku)) {
                return response(json_encode(array('error' => 1, 'msg' => 'Error while deleting to favourits')));
            }

            $verify = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->where('sku', $sku);
            if ($verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array('error' => 0)));
                } else {
                    return response(json_encode(array('error' => 1, 'msg' => 'Error while deleting to favourits')));
                }
            }
            return response(json_encode(array('msg' => 'No product found')));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
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
        if (Auth::check() && DashboardController::check(true)) {
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('code'));
            $code = str_replace(' ', '', $code);
            $category = sanitize($request->input('category'));
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            $imageName = "placeholder.svg";

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($name) || empty($code) || empty($category) || empty($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . str_replace(' ', '', $code) . '.' . $request->product_image->extension();
                    $request->product_image->move(public_path('assets/images/products'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $product = new Products();
            $product->pro_name = $name;
            $product->sku = $code;
            $product->price = $price;
            $product->cost = 0;
            $product->qty = $stock;
            $product->category = $category;
            $product->pro_image = $imageName;
            $product->pos_code = company()->pos_code;
            $product->supplier = null;
            $product->parent = '0';

            if ($product->save()) {
                $id = Products::orderBy('id', 'DESC')->first();
                $id = $id != null ? $id->sku : 0;
                return response(json_encode(array("error" => 0, "msg" => "Product Created Successfully", 'id' => $id)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function createVariant(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('code'));
            $code = str_replace(' ', '', $code);
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($name) || empty($code) || empty($price) || empty($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $parent = Products::where('id', sanitize($_GET['id']))->first();
            if ($parent == null) {
                return response(json_encode(array("error" => 1, "msg" => "Parent product not found")));
            }

            $product = new Products();
            $product->pro_name = $parent->pro_name.' - '.$name;
            $product->sku = $code;
            $product->price = $price;
            $product->cost = 0;
            $product->qty = $stock;
            $product->category = $parent->category;
            $product->pro_image = 'placeholder.svg';
            $product->pos_code = company()->pos_code;
            $product->supplier = null;
            $product->parent = $parent->id;

            if ($product->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Variant Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check()) {
            return redirect('/signin');
        }

        $product = Products::where('sku', sanitize($id))->get();

        if ($product && $product->count() > 0) {
            $variants = Products::where('parent', $product[0]->id)->get();
            return view('pos.add-product')->with(['product' => $product[0], 'variants' => $variants]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $products)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('code'));
            $code = str_replace(' ', '', $code);
            $category = sanitize($request->input('category'));
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            $imageName = "placeholder.svg";

            $id_verify = Products::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->where('id', '!=', $id)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($name) || empty($code) || empty($category) || empty($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked As '*'")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . str_replace(' ', '', $code) . '.' . $request->product_image->extension();
                    $request->product_image->move(public_path('assets/images/products'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $product = '';

            if ($request->hasFile('product_image')) {
                $product = Products::where('id', $id)->update([
                    "pro_name" => $name,
                    "sku" => $code,
                    "price" => $price,
                    "cost" => null,
                    "qty" => $stock,
                    "category" => $category,
                    "pro_image" => $imageName,
                ]);
            } else {
                $product = Products::where('id', $id)->update([
                    "pro_name" => $name,
                    "sku" => $code,
                    "price" => $price,
                    "cost" => null,
                    "qty" => $stock,
                    "category" => $category,
                ]);
            }

            if ($product) {
                return response(json_encode(array("error" => 0, "msg" => "Product Updated Successfully", 'sku' => $code)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function updateVariant(Request $request, Products $products)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('sku'));
            $code = str_replace(' ', '', $code);
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('qty'));
            $imageName = "placeholder.svg";

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->where('id', '!=', $id)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code '$code' Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($name) || empty($code) || empty($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked As '*'")));
            }

            $product = Products::where('id', $id)->update([
                "pro_name" => $name,
                "sku" => $code,
                "price" => $price,
                "qty" => $stock,
            ]);

            if ($product) {
                return response(json_encode(array("error" => 0, "msg" => "Variant Updated Successfully", 'sku' => $code)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $sku = sanitize($request->input('sku'));
            $verify = Products::where('sku', $sku);
            $product = $verify->get();
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    products::where('parent', $product[0]->id)->delete();
                    return response(json_encode(array("error" => 0, "msg" => "Product deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Product not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
