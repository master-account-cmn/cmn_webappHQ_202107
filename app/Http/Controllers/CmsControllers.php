<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Contents;
use App\Contents_list;
use App\Schedule;
use App\Pointmap;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class CmsControllers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('top');
    }

    // ---------------------User---------------------
    public function user_add(){
        if(Auth::user()->authority==10){
            return view('user_add');
        }else{
            return view('top');
        }
    }
    // userList表示
    public function user_list(){
        // loginUser:一般
        if( Auth::user()->authority == 10){
            $users = User::orderBy('created_at','asc')->where('delete_flag','0')->get();
        }else{
            $users = User::where('id',Auth::id())->get();
        }

        return view('user_list',['users'=>$users]);
    }
    //新規登録処理
    public function user_store(Request $request)
    {
        Log::debug($request);
        if($request->password !== $request->c_password)
        {
            $list = array("message" => 'error', "status" => 'error','code' => 422);
            return response()->json($list);

        }elseif($request->email !== $request->c_email){
            $list = array("message" => 'error', "status" => 'error','code' => 400);
            return response()->json($list);
        }else{

            $validator = Validator::make($request->all(),[
                'name' => 'required|alpha_dash|max:255|min:1',
                'password' => 'required|alpha_dash|max:255|min:7',
                'display_name' => 'required|max:255',
                'email' => 'email|unique:users',
                'authority' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors(), 422);
                Log::debug($validator);
            }
            try{
                DB::beginTransaction();
                $user = new User;
                $user->name = $request->name;
                $user->password = $request->password;
                $user->display_name = $request->display_name;
                $user->authority = $request->authority;
                $user->email = $request->email;
                $response=$user->save();
                DB::commit();
                return response()->json($response);
            }catch(Exception $e){
                DB::rollback();
                return response()->json($e);
            }
        }
    }
    // user_name検索機能
    public function getUsersBySearchName($userName)
    {
        $users = User::where('name', $userName)->orderBy('id', 'desc')->get();
        return response()->json($users);
    }
    // deleteフラグの変更
    public function user_delete(Request $request){
        $id = $request->id;
        try{
            DB::beginTransaction();
            User::where('id',$id)->update(['delete_flag' => 1]);
            DB::commit();
            return response()->json(true);
        }catch(Exception $e){
            DB::rollback();
            return response()->json($e);
        }

    }

    // 編集画面に遷移
    public function user_modify(Request $request){
        $id = $request->id;
        $user = User::where('id',$id)->first();
        return view('user_modify',['user'=>$user]);
    }
    // userの編集
    public function user_update(Request $request){
        $id = $request->id;
        $request=$request->all();
        Log::debug($request);
        try{
            DB::beginTransaction();
            foreach($request as $key =>$value){
                if($key == 'password'){
                    $user = User::where('id',$id)->first();
                    if($user->password !== $value){
                        return response()->json(false);
                        DB::rollback();
                    }
                }
                if( $key!=='id' && $key!=='new_password'){
                    User::where('id',$id)->update([$key => $value]);
                }elseif($key == 'new_password'){
                    User::where('id',$id)->update(['password' => $value]);
                }
            }
            DB::commit();
            return response()->json(true);
        }catch(Exception $e){
            DB::rollback();
            return response()->json($e);
        }

    }

    // ---------------------Contents---------------------
    public function content_add(){
        return view('content_add');
    }
    // userList表示
    public function contents_list(){
        $contents = Contents::orderBy('created_at','asc')->where('delete_flag','0')->get();
        return view('content_list',['contents'=>$contents]);
    }
    // contentの登録
    public function content_store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'display_name' => 'required|max:255',
            'content' => 'required|file|mimes:png,jpg,jpeg,mp4,wav',
        ]);
        if($validator->fails()){
            Log::debug($validator->errors()->toArray());
            $error = ['msg'=>$validator->errors()->toArray()];
            return response()->json($error, 422,[],JSON_UNESCAPED_UNICODE,);
        }
        // cid取得
        $file = $request->content;
        $cid = Contents::max('id')+1;
        if(preg_match('/^video\/*/',$file->getClientMimeType())){
            $cid = 'v'.$cid;
            $content_type = 10;
        }else{
            $cid = 'i'.$cid;
            $content_type = 1;
        }
        try{
            // storage
            $path = $file->storeAs('/public',$file->getClientOriginalName());
            // DB
            DB::beginTransaction();
            $content = new Contents;
            $content->cid = $cid;
            $content->name = $request->name;
            $content->display_name = $request->display_name;
            $content->content_type = $content_type;
            $content->file_name = $file->getClientOriginalName();
            $content->file_type = $file->getClientMimeType();
            $content->file_size = $request->file_size;
            $content->display_time = $request->content_time;
            $response=$content->save();
            DB::commit();
            return response()->json('success');

        }catch(Exception $e){
            DB::rollback();
            return response()->json($e);
        }
    }
    // contentの削除
    public function content_delete(Request $request){
        $id = $request->id;
        try{
            $content = Contents::find($id);
            // storageから削除
            $response=Storage::delete('public/' . $content->file_name);
            if($response==1){
                // DBから削除
                DB::beginTransaction();
                Contents::where('id',$id)->update(['delete_flag' => 1]);
                DB::commit();
                return response()->json('success');
            }else{
                return response()->json('fail');
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json($e->errors(), 422);
        }
    }
    // 編集画面に遷移
    public function content_modify(Request $request){
        $id = $request->id;
        $content = Contents::where('id',$id)->first();
        return view('content_modify',['content'=>$content]);
    }
    // contentの編集
    public function content_update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'display_name' => 'required|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
            Log::debug($validator);
        }
        $id = $request->id;
        try{
            DB::beginTransaction();
            $response = Contents::where('id',$id)->update(['name' => $request->name,'display_name' => $request->display_name]);
            DB::commit();
            return response()->json($response);
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }

    }
    // ---------------------schedule---------------------
    public function schedule_add(){
        return view('schedule_add');
    }
    public function get_content(Request $request)
    {
        try{
            $cid = $request->cid;
            if(empty($cid)){
                $result = Contents::orderBy('created_at','asc')->where('delete_flag','0')->get();
            }else{
                $result = Contents::where('cid',$cid)->get();
            }
            return response()->json($result);
        }catch(Exception $e){
            Log::debug($e);
            return response()->json($e);
        }

    }
    // DB保存
    public function schedule_store(Request $request){
        $validator = Validator::make($request->all(),[
            'schedule_name' => 'required|max:255',
            's_start' => 'required|date|max:255',
            's_end' => 'required|date|max:255',
            's_list' => 'required|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
            Log::debug($validator);
        }
        try{
            DB::beginTransaction();
            $schedule = new Schedule;
            $schedule->schedule_name = $request->schedule_name;
            $schedule->s_start = $request->s_start;
            $schedule->s_end = $request->s_end;
            $schedule->s_list = $request->s_list;
            $schedule->save();

            $list = new Contents_list;
            $list->sid = $schedule->id;
            $list->list = $request->s_list;
            $list->save();
            DB::commit();

            return response()->json('success');
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }
    }
    // schedule_list表示
    public function schedule_list(){
        $schedule = Schedule::orderBy('created_at','asc')->where('delete_flag','0')->get();

        return view('schedule_list',['schedule'=>$schedule]);
    }
    // scheduleの詳細表示
    public function schedule_display(Request $request){
        try{
            $id = $request->id;
            $schedule = Schedule::where('id',$id)->first();
            $list = json_decode($schedule->s_list,true);
            if($list){
                foreach($list as $key => $value){
                    $contents[] = Contents::where('cid',$value)->first();
                }
                return response()->json($contents);
            }
        }catch(Exception $e){
            Log::debug($e);
            return response()->json($e);
        }
    }
    // 編集画面に遷移
    public function schedule_modify(Request $request){
        $id = $request->id;
        $schedule = Schedule::where('id',$id)->first();
        $list = json_decode($schedule->s_list,true);
            if($list){
                foreach($list as $key => $value){
                    $contents[] = Contents::where('cid',$value)->first();
                }
            }
        return view('schedule_modify',['schedule'=>$schedule,'contents'=>$contents]);
    }
    // scheduleテーブルの更新
    public function schedule_update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            's_start' => 'required|date|max:255',
            's_end' => 'required|date|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
            Log::debug($validator);
        }
        $sid = $request->id;
        try{
            DB::beginTransaction();
            $response = Schedule::where('id',$sid)->update(['schedule_name' => $request->name,'s_start' => $request->s_start,'s_end' => $request->s_end]);
            DB::commit();
            return response()->json($response);
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }
    }
    // scheduleテーブル(list)の更新
    public function schedule_content_update(Request $request){
        $validator = Validator::make($request->all(),[
            's_list' => 'required|max:255',
        ]);
        if($validator->fails()){
            Log::debug($validator);
            return response()->json($validator->errors(), 422);
        }
        $sid = $request->id;
        Log::debug($request->s_list);
        try{
            DB::beginTransaction();
            $response[] = Schedule::where('id',$sid)->update(['s_list' => $request->s_list]);
            $response[] = Contents_list::where('id',$sid)->update(['list' => $request->s_list]);
            DB::commit();
            return response()->json($response);
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }
    }
    // delete schedule
    public function schedule_delete(Request $request){
        $id = $request->id;
        try{
            DB::beginTransaction();
            Schedule::where('id',$id)->update(['delete_flag' => 1]);
            Contents_list::where('id',$id)->update(['delete_flag' => 1]);
            DB::commit();
            return response()->json('success');
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }

    }
    // ------------------------googleMap-------------------------
    public function gmap(){
        $list = Pointmap::orderBy('created_at','asc')->get();
        return view('map',['list' => $list]);
    }
    public function ride_map(Request $request){
        $start_time = strtotime($request->start_time);
        $end_time = strtotime($request->end_time);
        $ride_list = Pointmap::orderBy('ride_time','asc')->where('ride_flag',1)->get();
        if(count($ride_list) == 0){
            return response()->json(false);
            exit;
        }
        $list = [];
        foreach($ride_list as $key => $value){
            $ride_time = strtotime($value['ride_time']);
            if($start_time < $ride_time && $ride_time < $end_time){
                $list[] = $value;
            }
        }
        if(count($list) == 0){
            return response()->json(false);
            exit;
        }
        Log::debug($list);
        return response()->json($list);
        exit;
    }

    public function normal() {
        return view('normal');
      }
}

