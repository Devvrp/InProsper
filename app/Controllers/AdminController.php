<?php
namespace InProsper\Controllers;
use InProsper\Core\Auth;
use InProsper\Core\Database;
use InProsper\Repositories\MerchantRepository;
use InProsper\Repositories\OrderRepository;
use InProsper\Repositories\ProductRepository;
use InProsper\Repositories\NotificationRepository;
final class AdminController {
    public function __construct(private MerchantRepository $merchants,private OrderRepository $orders,private ProductRepository $products) {}
    public function dashboard(): void {
        Auth::requireRole('admin'); $db=Database::connection();
        $pending=$this->merchants->pending(); $verified=$this->merchants->verifiedCount(); $orders=$this->orders->countSuccessful(); $gross=$this->orders->grossTransactions(); $rescued=$this->products->countRescuedMeals();
        $rejected=(int)$db->query('SELECT COUNT(*) FROM merchants WHERE verification_status="ditolak"')->fetchColumn();
        render('admin/dashboard',['title'=>'Dashboard admin','pending'=>$pending,'verified'=>$verified,'successful_orders'=>$orders,'gross'=>$gross,'rescued'=>$rescued,'rejected'=>$rejected]);
    }
    public function review(): void {
        Auth::requireRole('admin'); $id=(int)($_POST['merchant_id']??0); $decision=(string)($_POST['decision']??''); $reason=trim((string)($_POST['reason']??'')); $m=$this->merchants->byId($id); if(!$m || $m['verification_status']!=='menunggu') throw new \RuntimeException('Pengajuan tidak ditemukan atau telah diproses.');
        if($decision==='approve') { $this->merchants->review($id,(int)Auth::user()['user_id'],'disetujui',null); flash('success','Merchant disetujui. Akunnya dapat mulai berjualan.'); }
        elseif($decision==='reject' && $reason!=='') { $this->merchants->review($id,(int)Auth::user()['user_id'],'ditolak',$reason); (new NotificationRepository())->create((int)$m['user_id'],'Pengajuan merchant ditolak. Alasan: '.$reason); flash('success','Pengajuan merchant ditolak dengan alasan.'); }
        else throw new \RuntimeException('Pilih keputusan yang valid dan isi alasan jika menolak.');
        redirect('?page=admin');
    }
}
