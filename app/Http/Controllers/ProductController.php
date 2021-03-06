<?php

namespace App\Http\Controllers;


use Faker\Factory;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
class ProductController extends Controller
{

    public function index()
    {
        return view('product.product');
    }

    public function add()
    {
        $categories = Category::with('products')->where('is_enabled', true)->get();
        return view('product.product_add', compact('categories'));
    }

    public function get(Request $req)
    {

//        \DB::enableQueryLog();
        $product = Product::with('category', 'user')->where('id',(int)$req->route('product_id'))->first();
        if (!$product == null) {
            $product_cat = Category::with(['products' => function($query) use ($product) {
                $query->limit(8)
                    ->orderByRaw('created_at DESC')
                    ->where('id', '!=', $product->id);
            }])
                ->where('id',$product->category_id)
                ->where('is_enabled', true)
                ->first();
            return view('product.product', compact('product','product_cat'));
        } else {
            return back();
        }
        //        dd(\DB::getQueryLog());
//        dd($product);

    }

    public function edit(Request $req)
    {

//        \DB::enableQueryLog();
        $product = Product::with(['category'=>  function ($q){
            $q->where('is_enabled', true);
        }, 'user'])->where('id',(int)$req->route('product_id'))->where('user_id', Auth::id())->first();
//        dd(\DB::getQueryLog());
//        dd($product);
        if ($product !== null) {
            $categories = Category::with('products')->where('is_enabled', true)->get();
            return view('product.product_edit', compact('product', 'categories'));
        }  else {
            return back();
        }
    }

    public function search(Request $request) {
        $categories      = Category::with('products')->get();
        $search   = $request->input('search');
        $cat   = $request->input('category_id');
        $category      = Category::with('products')->where('is_enabled',true)->where('id',(int)$cat)->first();
        if (isset($search)) {
            $products = Product::

            when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->orWhere('name', 'like', $search . '%');
                });
            })
                ->orderByRaw('created_at DESC')->get();
            $prod_count = $products->count();
            return view('product.products', compact('products', 'categories', 'category', 'prod_count', 'cat', 'search'));
        } elseif ($cat) {
            $products = Product::

            when($cat, function ($q) use ($cat) {
                $q->where(function ($q) use ($cat) {
                    $q->orWhere('category_id', 'like', $cat . '%');
                });
            })
                ->orderByRaw('created_at DESC')->get();
            $prod_count      = $products->count();
            return view('product.products', compact('products', 'categories', 'category', 'prod_count', 'cat', 'search'));
        } else {
            return back();
        }

    }

    public function submit(ProductRequest $req) {

        $model = new Product;

        $model->fill([
            'name'         => $req->input('name'),
            'category_id'  => $req->input('category_id'),
            'user_id'      => Auth::id(),
            'description'  => $req->input('description'),
            'phone_number' => $req->input('phone_number'),
            'location'     => $req->input('location'),
            'price'        => $req->input('price'),
            'img'          => '',
        ]);

        $model->save();

        $model->update([
            'img' => $model->img_url,
        ]);

        $img = Image::make($req->file('image'));
        $height = $img->height();
        $width = $img->width();
        if($height >= 601) {
            $img->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        if($width >= 601) {
            $img->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $img->save($model->img_path);

        if (0) {
            for ($i = 1; $i <= 5;  $i++) {
                $faker = Factory::create('ru_RU');

                $model = new Product;
                $model->fill([
                    'name'         => $faker->realText($maxNbChars = 20, $indexSize = 1),
                    'category_id'  => rand(1,8),
                    'user_id'      => rand(1,11),
                    'description'  => $faker->realText($maxNbChars = 400, $indexSize = 4),
                    'phone_number' => $faker->phoneNumber,
                    'location'     => $faker->address,
                    'price'        => rand(1000,50000),
                    'img'          => 'storage/!/thumbs/products/' . $faker->image('storage/!/thumbs/products/', 640,480, null, false)
                ]);
                $model->save();
            }
        }

        return  redirect()->route('product.get', $model->id);
    }
}
