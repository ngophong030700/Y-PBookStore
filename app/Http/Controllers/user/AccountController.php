<?php

namespace App\Http\Controllers\user;
use App\Models\Account;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Cookie;
use Mail;
use App\Mail\MailContact;
use App\Mail\MailResponse;
use App\Models\FavoriteBook;
class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Account::where('Id', Cookie::get('UserId'))->first();
        $sach_yeu_thich = FavoriteBook::where('Id_TK', Cookie::get('UserId'))->get();
        $don_hang = Order::where('Id_KH', Cookie::get('UserId'))->get();
        //return dd($user);
        return View('user.pages.usermanagement', $user, ['sach_yeu_thich'=>$sach_yeu_thich, 'don_hang'=>$don_hang]);
        //
    }

    public function updateinfomation(Request $request, $id)
    {
        //
        $tai_khoan = Account::find($id);
        if($request->file('Anh_Dai_Dien')!= null){
            $name = $request->file('Anh_Dai_Dien')->getClientOriginalName();     
            $anh_dai_dien = $request->file('Anh_Dai_Dien')->move('images', $name);
        } 
        else $anh_dai_dien=$tai_khoan->Anh_Dai_Dien;
      
        $tai_khoan->Ho_Ten=$request['Ho_Ten'];
        $tai_khoan->Ngay_Sinh=$request['Ngay_Sinh'];
        $tai_khoan->Gioi_Tinh=$request['Gioi_Tinh'];
        $tai_khoan->Anh_Dai_Dien=$anh_dai_dien;
        $tai_khoan->So_Dien_Thoai=$request['So_Dien_Thoai'];
        $tai_khoan->Dia_Chi=$request['Dia_Chi'];
        //$tai_khoan->Gioi_Tinh=$request['Gioi_Tinh'];
        $tai_khoan->save();
        return redirect()->back();
    }

    public function addfavoritebook(Request $request)
    {
        $sach = $request['Id_Sach'];
        $check = FavoriteBook::where([ ['Id_Sach', '=', $sach], ['Id_TK', '=', Cookie::get('UserId')] ])->first();
        if($check == null){
            $sach_yeu_thich=FavoriteBook::create([
                'Id_Sach'=>$sach,
                'Id_TK'=>Cookie::get('UserId')
            ]);
            return response()->json('???? th??m v??o s??ch y??u th??ch!');
        }           
        return response()->json('S??ch ???? y??u th??ch');
    }

    public function deletefavoritebook(Request $request)
    {
        $sach = $request['Id'];
        $sach_yt = FavoriteBook::find($sach);
        if($sach_yt != null){
            $sach_yt->delete();
        }
        $sach_yeu_thich = FavoriteBook::where('Id_TK', Cookie::get('UserId'))->get();         
        return response()->json($sach_yeu_thich);
    }

    //?????i m???t kh???u
    public function changepass(Request $request)
    {
        $mk = $request['Pass_New'];
        $mk_cu = $request['Pass_Old'];
        $tai_khoan = Account::find(Cookie::get('UserId'));
        if(Hash::check($mk_cu, $tai_khoan->Mat_Khau) == false) return response()->json("M???t kh???u c?? kh??ng ????ng!");
        if($tai_khoan != null){
            $tai_khoan->Mat_Khau = Hash::make($mk);
        }
        $tai_khoan->save();      
        return response()->json("C???p nh???t th??nh c??ng!");
    }

    ///mail li??n h???
    public function mailcontact(Request $request)
    {
        $data = [
            'name' => $request['Name'],
            'email' => $request['Email'],
            'phone' => $request['Phone'],
            'content' => $request['Content'],
        ];
        Mail::to('ypbookstore2018@gmail.com')->send(new MailContact($data));
        //password t??i kho???n: a@123456 
        Mail::to($request['Email'])->send(new MailResponse());    
        return redirect()->back();
        //return dd($data);
    }

    // Gi??? h??ng
    public function addcart(Request $request)
    {
        //
        $sach = $request['Id_Sach'];

        if($request['So_Luong']){
            $soluong = $request['So_Luong'];
        }
        $check = Cart::where([ ['Id_Sach', '=', $sach], ['Id_TK', '=', Cookie::get('UserId')] ])->first();
        if($check == null){
            $gio_hang=Cart::create([
                'Id_Sach'=>$sach,
                'Id_TK'=>Cookie::get('UserId'),
                'So_Luong'=>$soluong
            ]);
        }
        else
        {
            $check->So_Luong = $check->So_Luong + $soluong;
            $check->save();
        }      
        
        // L???y s??? l?????ng sp trong gi??? h??ng
        $g_hang = Cart::where('Id_TK', Cookie::get('UserId'))->get();
        $s_luong = 0;
        if ($g_hang != null){
            foreach ($g_hang as $cart)
            {
                $s_luong = $s_luong + $cart->So_Luong;
            }
        }
        return response()->json($s_luong);
    }

    public function deletecart($id)
    {
        //
        $cart = Cart::find($id);
        if($cart != null){
            $cart->delete();
        }           
        return redirect()->back();
    }

    public function updatecart(Request $request)
    {
        //
        $id = $request['Id'];
        $cart = Cart::find($id);
        if($cart != null){
            $cart->So_Luong = $request['So_Luong'];
            $cart->save();
        }           
        return redirect()->back();
    }

    public function payment(Request $request)
    {
        //
        $idsach = $request['Id'];
        $soluong = $request['So_Luong'];
        $tai_khoan = Account::find(Cookie::get('UserId'));
        if($idsach != null && $soluong != null)
        {
            $sach = Book::where('Id', $idsach)->get();          
            foreach($sach as $book)
            $book->So_Luong = $soluong;
        }
        return View('user.pages.checkout', $tai_khoan, ['sach'=> $sach]);
    }

    public function paymentcart()
    {
        //
        $sach = Cart::where('Id_TK', Cookie::get('UserId'))->get();
        $tai_khoan = Account::find(Cookie::get('UserId'));
        return View('user.pages.checkout', $tai_khoan, ['sach'=> $sach]);
    }

    public function createpaymentcart(Request $request)
    {
        //
        $dia_chi = $request['Dia_Chi'];
        $tong_tien = $request['Tong_Tien'];
        $pt_thanh_toan = $request['Hinh_Thuc'];
        $gio_hang = Cart::where('Id_TK', Cookie::get('UserId'))->get();
        if($dia_chi != null && $tong_tien != null && $pt_thanh_toan != null)
        {
            $don_hang=Order::create([
                'Tong_Tien'=>$tong_tien,
                'Id_KH'=>Cookie::get('UserId'),
                'PT_Thanh_Toan'=>$pt_thanh_toan,
                'Dia_Chi_Giao_Hang'=>$dia_chi,
                'Trang_Thai'=>0
            ]);
            foreach ($gio_hang as $sp)
            {
                $ctdh=OrderDetail::create([
                    'Id_Sach'=>$sp->Id_Sach,
                    'Id_DH'=>$don_hang->Id,
                    'So_Luong'=>$sp->So_Luong,
                    'Trang_Thai'=>0
                ]);
                $sach = Book::find($sp->Id_Sach);
                $sach->So_Luong = $sach->So_Luong - $sp->So_Luong;
                $sach->save();
                $sp->delete();
            }
        }
        return response()->json('?????t h??ng th??nh c??ng!');
    }

    public function createpaymentquick(Request $request)
    {
        //
        $dia_chi = $request['Dia_Chi'];
        $tong_tien = $request['Tong_Tien'];
        $pt_thanh_toan = $request['Hinh_Thuc'];
        $id_sach = $request['Id_Sach'];
        $so_luong = $request['So_Luong'];
        if($dia_chi != null && $tong_tien != null && $pt_thanh_toan != null && $id_sach != null && $so_luong != null)
        {
            $don_hang=Order::create([
                'Tong_Tien'=>$tong_tien,
                'Id_KH'=>Cookie::get('UserId'),
                'PT_Thanh_Toan'=>$pt_thanh_toan,
                'Dia_Chi_Giao_Hang'=>$dia_chi,
                'Trang_Thai'=>0
            ]);

            $ctdh=OrderDetail::create([
                'Id_Sach'=>$id_sach,
                'Id_DH'=>$don_hang->Id,
                'So_Luong'=>$so_luong,
                'Trang_Thai'=>0
            ]);

            $sach = Book::find($id_sach);
            $sach->So_Luong = $sach->So_Luong - $so_luong;
            $sach->save();
        }
        return response()->json('?????t h??ng th??nh c??ng!');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderdetail($id)
    {
        //
        $don_hang = Order::find($id);
        $ctdh = OrderDetail::where('Id_DH', $id)->get();
        $kh = Account::where('Id', Cookie::get('UserId'))->get();
        return View('user.pages.orderdetail', $don_hang, ['ctdh'=>$ctdh, 'kh'=>$kh]);
    }

    public function cancelorder($id)
    {
        //
        $don_hang = Order::find($id);
        if ($don_hang != null) {
            $don_hang->Trang_Thai = 4;
            $don_hang->save();
        }
        $ctdh = OrderDetail::where('Id_DH', $id)->get();
        foreach ($ctdh as $ct)
        {
            //c???p nh???t tr???ng th??i ????n h??ng
            $savectdh = OrderDetail::find($ct->Id);
            $savectdh->Trang_Thai = 1;
            $savectdh->save();
            //c???p nh???t s??? l?????ng s??ch
            $savesach = Book::find($ct->Id_Sach);
            $savesach->So_Luong = $savesach->So_Luong + $ct->So_Luong;
            $savesach->save();
        }
        return redirect()->back();
    }

    public function create()
    {
        //
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
