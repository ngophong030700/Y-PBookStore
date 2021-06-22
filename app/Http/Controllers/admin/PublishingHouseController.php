<?php

namespace App\Http\Controllers\admin;
use App\Models\PublishingHouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class PublishingHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __construct()
    {
        $this->viewprefix='admin.pages.PublishingHouse.';
        $this->viewnamespace='admin/pages/PublishingHouse';
    }
    public function index()
    {
        //
        $nha_xuat_ban=PublishingHouse::all();
        return view($this->viewprefix.'publishinghouse',compact('nha_xuat_ban'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view($this->viewprefix.'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $nha_xuat_ban= new PublishingHouse();
        $this->validate($request, [
            'Ten_NXB'=>'required',
            'Dia_Chi'=>'required',
            'So_Dien_Thoai'=>'required',
            'Email'=>'required',
            'TrangThai' => 'required',
           
        ]);
        $nha_xuat_ban->Ten_NXB=$request->Ten_NXB;
        $nha_xuat_ban->Dia_Chi=$request->Dia_Chi;
        $nha_xuat_ban->So_Dien_Thoai=$request->So_Dien_Thoai;
        $nha_xuat_ban->Email=$request->Email;
        $nha_xuat_ban->TrangThai=$request->TrangThai;
        
        //if(Category::create($request->all()))
        if($nha_xuat_ban->save())
        {
            Session::flash('message', 'successfully!');
        }
        else
            Session::flash('message', 'Failure!');
        return redirect()->route('quan-ly-nha-xuat-ban.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
