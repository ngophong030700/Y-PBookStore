@extends('admin.layout')
@section('content')
<style>
    .row{
        padding-top:20px !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">CHỈNH SỬA</h3>
                </div>
            </div>
        </div>
        <div class="container">
            <!-- form start -->
                <form action="{{route('quan-ly-don-hang.update',$don_hang->Id)}}" method="POST" enctype="multipart/form-data">
                @csrf
               @method('PUT')
                <div class="row">
                    <label for="exampleInputTitle">Trạng thái</label>
                    <select style="border: 1px solid #CED4DA;border-radius: 4px; outline: none;" class="form-control" name="Trang_Thai"  placeholder="Status">
                        @if($don_hang->Trang_Thai==0)
                        <option value="0" selected>Chưa hoàn thành</option>
                        <option value="1">Đã boàn thành</option>
                        @elseif($don_hang->Trang_Thai==1)
                        <option value="0" >Chưa hoàn thành</option>
                        <option value="1" selected>Đã boàn thành</option>
                        @endif
                    </select>  
                </div>
                <div class="row" style="float:right">
                  <button type="submit" class="btn btn-success"><i class="fas fa-save"></i></button> &nbsp;
                  <button type="cancel" class="btn btn-secondary" style="margin-left: 15px;margin-right: 5px; color:white"><i class="fas fa-window-close"></i></button>
                </div>
              </form>
        </div>
    </div>
</div>
@stop