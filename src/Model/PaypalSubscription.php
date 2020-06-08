<?php

namespace ShahBurhan\LaravelPayPal\Model;

use Illuminate\Database\Eloquent\Model;

class PaypalSubscription extends Model
{
    protected $guarded = ['id'];

    public static function createSubscription($request){
    	static::create(['subscription_id'=>$request->id, 'user_id'=>auth()->id(), 'subscription_plan_id'=>session('plan')->id, 'status'=>$request->status]);
    } 
    public static function flushPending(){
    	static::where(['user_id' => auth()->id(), 'status'=>'APPROVAL_PENDING'])->delete();
    } 
    public static function updateStatus($data){
    	$subscription =  static::where(['subscription_id'=>$data['id'], 'user_id'=>auth()->id()])->first();
    	$subscription->status = $data['status'];
    	$subscription->subscription_ends = date('Y-m-d', strtotime($data['billing_info']['next_billing_time']));
    	$subscription->save();
        
        return $subscription;
    }
}