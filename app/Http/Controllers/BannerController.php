<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Auth;
use Session;

class BannerController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
       $this->middleware('auth');
   }

   /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
   public function index()
   {
       $banners = Banner::orderBy('created_at', 'desc')->paginate(5);
       return view('admin/pages/banner/index', compact('banners'));
   }
   public function add()
   {
       return view('admin/pages/banner/add');
   }
   public function store(request $req)
   {
       if(Auth::check()) {
        $req->validate([
            'bannerimage' => 'required | mimes:jpg,jpeg,png,bmp,tiff',
        ],$messages = [   
            'mimes' => 'Please insert image only'
        ]);
           $banner = new Banner;
           if($req->hasFile('bannerimage')) {
            $ext = $req->bannerimage->getClientOriginalExtension();
            $time = time();
            $fileName = hash('ripemd128', $time).$ext;
            $banner->image = $fileName;
            $req->bannerimage->storeAs('banner/img', $fileName,'public');
           }
           $banner->save();
           $req->session()->flash('bannerAdd', 'The banner is added ssuccessfully!');
           return redirect()->route('banner.list');
       }
   }
   public function edit($bannerId) {
       if(Auth::check()) {
           $banner = Banner::find($bannerId);
           return view('admin/pages/banner/edit', compact('banner'));
       }
   }
   public function update(request $req) {
       if(Auth::check()) {
        $req->validate([
            'bannerimage' => 'mimes:jpg,jpeg,png,bmp,tiff',
        ],$messages = [   
            'mimes' => 'Please insert image only'
        ]);
           $banner = Banner::find($req->input('bannerId'));
           if($req->hasFile('bannerimage')) {
            $ext = $req->bannerimage->getClientOriginalExtension();
            $time = time();
            $fileName = hash('ripemd128', $time).$ext;
            $banner->image = $fileName;
            $req->bannerimage->storeAs('banner/img', $fileName,'public');
           }
           $banner->save();
           $req->session()->flash('bannerEdit', 'The banner is edited ssuccessfully!');
           return redirect()->route('banner.list');
       }
   }
   public function delete($bannerId) {
       if(Auth::check()) {
           Banner::where('id', $bannerId)->delete();
           Session::flash('bannerDelete', 'The banner is deleted ssuccessfully!');
           return redirect()->route('banner.list');
       }
   }
}
