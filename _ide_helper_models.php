<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\File
 *
 * @property int $id
 * @property string|null $code Mã
 * @property string|null $name Tên
 * @property string $extension Phần mở rộng
 * @property string $mime_type MIME Type
 * @property int $size Dung lượng
 * @property string $path Đường dẫn
 * @property string $table_name Tên bảng
 * @property int $table_id Mã bảng
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedDate($value)
 */
	class File extends \Eloquent {}
}

namespace App{
/**
 * App\DriverTruck
 *
 * @property int $id
 * @property int $driver_id 1
 * @property int $truck_id 1
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DriverTruck whereUpdatedDate($value)
 */
	class DriverTruck extends \Eloquent {}
}

namespace App{
/**
 * App\Transport
 *
 * @property int $id
 * @property string $code Mã
 * @property string $transport_date Ngày vận chuyển
 * @property string $type1 Đơn hàng thường hay khống
 * @property string $type2 Đã xuất cho khách hàng - HĐ hoặc PTT - Xuất đủ hay chưa
 * @property string $type3 Đã xuất cho nhà xe - PTT - Xuất đủ
 * @property int $quantum_product Số lượng sản phẩm
 * @property float $revenue Doanh thu
 * @property float $profit Lợi nhuận
 * @property float $receive Nhận
 * @property float $delivery Giao xe
 * @property float $carrying Bốc xếp
 * @property float $parking Neo đêm
 * @property float $fine Công an
 * @property float $phi_tang_bo Phí tăng bo
 * @property float $add_score Thêm điểm
 * @property float $delivery_real Giao xe thực tế
 * @property float $carrying_real Bốc xếp thực tế
 * @property float $parking_real Neo đêm thực tế
 * @property float $fine_real Công an thực tế
 * @property float $phi_tang_bo_real Phí tăng bo thực tế
 * @property float $add_score_real Thêm điểm thực tế
 * @property string|null $voucher_number Số chứng từ
 * @property string $quantum_product_on_voucher Số lượng sản phẩm trên chứng từ
 * @property string|null $receiver Người nhận
 * @property string|null $receive_place Nơi nhận
 * @property string|null $delivery_place Nơi giao
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $truck_id
 * @property int $product_id
 * @property int $customer_id
 * @property int $postage_id
 * @property string $type
 * @property int $fuel_id Mã Dầu/Nhớt nếu trong công thức có giá Dầu/Nhớt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereAddScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereAddScoreReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCarrying($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCarryingReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereDeliveryPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereDeliveryReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereFine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereFineReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereFuelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereParking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereParkingReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport wherePhiTangBo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport wherePhiTangBoReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport wherePostageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereQuantumProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereQuantumProductOnVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereReceivePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereTransportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereType1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereType2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereType3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transport whereVoucherNumber($value)
 */
	class Transport extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $code Mã
 * @property string $fullname Họ tên
 * @property string|null $username Tài khoản
 * @property string|null $password Mật khẩu
 * @property string|null $address Địa chỉ
 * @property string|null $phone Điện thoại
 * @property string|null $birthday Ngày sinh
 * @property string $sex Giới tính
 * @property string|null $email Email
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 */
	class User extends \Eloquent {}
}

namespace App{
/**
 * App\Customer
 *
 * @property int $id
 * @property string $code Mã
 * @property string|null $tax_code Mã số thuế
 * @property string $fullname Họ tên
 * @property string|null $address Địa chỉ
 * @property string|null $phone Điện thoại
 * @property string|null $email Email
 * @property float $limit_oil Số phần trăm khi giá dầu đạt mức này sẽ đổi cước phí
 * @property float $oil_per_postage Số phần trăm giá dầu/cước phí
 * @property string $finish_date Ngày kết thúc
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $customer_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereFinishDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereLimitOil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereOilPerPostage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereTaxCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedDate($value)
 */
	class Customer extends \Eloquent {}
}

namespace App{
/**
 * App\ProductCode
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property int $product_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductCode whereUpdatedAt($value)
 */
	class ProductCode extends \Eloquent {}
}

namespace App{
/**
 * App\FuelCustomer
 *
 * @property int $id
 * @property string $type
 * @property int $fuel_id Giá dầu làm mốc của khách hàng
 * @property int $customer_id
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereFuelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FuelCustomer whereUpdatedDate($value)
 */
	class FuelCustomer extends \Eloquent {}
}

namespace App{
/**
 * App\Cost
 *
 * @property int $id
 * @property string $code Mã
 * @property string $type
 * @property float $vat 1
 * @property float $after_vat Tổng chi phí (Chi phí sau khi có vat)
 * @property int|null $fuel_id
 * @property float|null $quantum_liter Số lít dầu/nhớt
 * @property string|null $refuel_date Ngày đổ dầu/nhớt
 * @property string|null $checkin_date Ngày đậu bãi
 * @property string|null $checkout_date Ngày ra bãi
 * @property int|null $total_day Tổng ngày đậu bãi
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $truck_id
 * @property int $invoice_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereAfterVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCheckinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCheckoutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereFuelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereQuantumLiter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereRefuelDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereTotalDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cost whereVat($value)
 */
	class Cost extends \Eloquent {}
}

namespace App{
/**
 * App\Product
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property int $product_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereProductTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App{
/**
 * App\FormulaSample
 *
 * @property int $id
 * @property string $code Mã
 * @property string $rule
 * @property string $name
 * @property int $index
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormulaSample whereUpdatedAt($value)
 */
	class FormulaSample extends \Eloquent {}
}

namespace App{
/**
 * App\Truck
 *
 * @property int $id
 * @property string $code Mã
 * @property string $area_code Mã vùng
 * @property string $number_plate Số xe
 * @property string|null $trademark Hãng xe
 * @property int $year_of_manufacture Năm sản xuất
 * @property string|null $owner Chủ xe
 * @property int $length Dài
 * @property int $width Rộng
 * @property int $height Cao
 * @property string $status
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $truck_type_id
 * @property int $garage_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereNumberPlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereTrademark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereTruckTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Truck whereYearOfManufacture($value)
 */
	class Truck extends \Eloquent {}
}

namespace App{
/**
 * App\Fuel
 *
 * @property int $id
 * @property string $code Mã
 * @property float $price Giá nhiên liệu
 * @property string $type
 * @property string $apply_date
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereApplyDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fuel whereUpdatedDate($value)
 */
	class Fuel extends \Eloquent {}
}

namespace App{
/**
 * App\InvoiceDetail
 *
 * @property int $id
 * @property float $paid_amt
 * @property string $payment_date Ngày trả
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $invoice_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail wherePaidAmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceDetail whereUpdatedDate($value)
 */
	class InvoiceDetail extends \Eloquent {}
}

namespace App{
/**
 * App\TransportVoucher
 *
 * @property int $id
 * @property int $transport_id
 * @property int $voucher_id
 * @property int $quantum Số lượng chứng từ
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereQuantum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereTransportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportVoucher whereVoucherId($value)
 */
	class TransportVoucher extends \Eloquent {}
}

namespace App{
/**
 * App\Position
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Position whereUpdatedAt($value)
 */
	class Position extends \Eloquent {}
}

namespace App{
/**
 * App\TruckType
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property float $weight Trọng tải
 * @property float $unit_price_park Đơn giá đậu bãi cho loại xe
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereUnitPricePark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TruckType whereWeight($value)
 */
	class TruckType extends \Eloquent {}
}

namespace App{
/**
 * App\Driver
 *
 * @property int $id
 * @property string $code Mã
 * @property string $fullname Họ tên
 * @property string|null $phone Điện thoại
 * @property string|null $birthday Ngày sinh
 * @property string $sex Giới tính
 * @property string|null $email Email
 * @property string|null $dia_chi_thuong_tru
 * @property string|null $dia_chi_tam_tru
 * @property string|null $so_chung_minh
 * @property string|null $ngay_cap_chung_minh
 * @property string|null $loai_bang_lai
 * @property string|null $so_bang_lai
 * @property string|null $ngay_cap_bang_lai
 * @property string|null $ngay_het_han_bang_lai
 * @property string $start_date Ngày vào làm
 * @property string|null $finish_date Ngày nghĩ việc
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereDiaChiTamTru($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereDiaChiThuongTru($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereFinishDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereLoaiBangLai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereNgayCapBangLai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereNgayCapChungMinh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereNgayHetHanBangLai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereSoBangLai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereSoChungMinh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereUpdatedDate($value)
 */
	class Driver extends \Eloquent {}
}

namespace App{
/**
 * App\Garage
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property string|null $address
 * @property string|null $contactor
 * @property string|null $phone
 * @property int $active Kích hoạt
 * @property int $garage_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereContactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereGarageTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Garage whereUpdatedAt($value)
 */
	class Garage extends \Eloquent {}
}

namespace App{
/**
 * App\Invoice
 *
 * @property int $id
 * @property string $code Mã
 * @property string $type1 HĐ or PTT thường hay khống
 * @property string $type2 Hóa đơn KH - PTT KH
 * @property string $type3 PTT xe
 * @property int $customer_id
 * @property float $total_revenue Tổng doanh thu
 * @property float $total_receive Tổng tiền nhận trước
 * @property int $truck_id
 * @property float $total_delivery Tổng tiền giao xe
 * @property float $total_cost_in_transport Tổng tiền chi phí (Bốc xếp, Neo đêm, Công an, Phí tăng bo, Thêm điểm)
 * @property float $total_cost Tổng tiền chi phí (Dầu, Nhớt, Đậu bãi, Khác)
 * @property float $total_pay Tổng tiền xuất HĐ hoặc PTT
 * @property float $vat VAT
 * @property float $after_vat Tổng tiền sau VAT (Tổng HĐ hoặc PTT)
 * @property float $total_paid Tổng tiền đã trả
 * @property string $invoice_date Ngày hóa đơn
 * @property string $payment_date Ngày thanh toán
 * @property string|null $receiver Người nhận
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereAfterVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalCostInTransport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereType1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereType2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereType3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereVat($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App{
/**
 * App\GarageType
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GarageType whereUpdatedAt($value)
 */
	class GarageType extends \Eloquent {}
}

namespace App{
/**
 * App\CustomerType
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerType whereUpdatedAt($value)
 */
	class CustomerType extends \Eloquent {}
}

namespace App{
/**
 * App\TransportInvoice
 *
 * @property int $id
 * @property int $transport_id
 * @property int $invoice_id
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereTransportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportInvoice whereUpdatedDate($value)
 */
	class TransportInvoice extends \Eloquent {}
}

namespace App{
/**
 * App\Role
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property string $router_link router link cho angular
 * @property string $icon_name icon cho aside
 * @property int $index vị trí thứ tự
 * @property int $active Kích hoạt
 * @property int $group_role_id Nhóm quyền
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereGroupRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereIconName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereRouterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App{
/**
 * App\ProductType
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductType whereUpdatedAt($value)
 */
	class ProductType extends \Eloquent {}
}

namespace App{
/**
 * App\Rule
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereUpdatedAt($value)
 */
	class Rule extends \Eloquent {}
}

namespace App{
/**
 * App\TransportFormula
 *
 * @property int $id
 * @property string $rule
 * @property string $name
 * @property string $value1
 * @property string|null $value2
 * @property int $active Kích hoạt
 * @property int $transport_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereTransportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TransportFormula whereValue2($value)
 */
	class TransportFormula extends \Eloquent {}
}

namespace App{
/**
 * App\StaffCustomer
 *
 * @property int $id
 * @property string $code Mã
 * @property string $fullname Họ tên
 * @property string|null $address Địa chỉ
 * @property string|null $phone Điện thoại
 * @property string|null $birthday Ngày sinh
 * @property string $sex Giới tính
 * @property string|null $email Email
 * @property string|null $position Chức vụ
 * @property int $active Kích hoạt
 * @property int $customer_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StaffCustomer whereUpdatedAt($value)
 */
	class StaffCustomer extends \Eloquent {}
}

namespace App{
/**
 * App\Unit
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereUpdatedAt($value)
 */
	class Unit extends \Eloquent {}
}

namespace App{
/**
 * App\Field
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $is_read
 * @property int $is_create
 * @property int $is_update
 * @property int $is_delete
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $role_id Quyền
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereIsCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereIsUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Field whereUpdatedDate($value)
 */
	class Field extends \Eloquent {}
}

namespace App{
/**
 * App\Formula
 *
 * @property int $id
 * @property string|null $code Mã
 * @property string $rule
 * @property string $name
 * @property string $value1
 * @property string|null $value2
 * @property int $index
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $postage_id Mã cước phí
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula wherePostageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Formula whereValue2($value)
 */
	class Formula extends \Eloquent {}
}

namespace App{
/**
 * App\Postage
 *
 * @property int $id
 * @property string $code Mã
 * @property float $unit_price Đơn giá trên mỗi đơn vị tính
 * @property float $delivery_percent Phần trăm giao xe
 * @property string|null $apply_date
 * @property int $change_by_fuel Tạo do nhiên liệu thay đổi
 * @property string|null $note Ghi chú
 * @property int $created_by Người tạo
 * @property int $updated_by Người sửa
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property int $customer_id
 * @property int $unit_id
 * @property string $type
 * @property int $fuel_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereApplyDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereChangeByFuel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereDeliveryPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereFuelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Postage whereUpdatedDate($value)
 */
	class Postage extends \Eloquent {}
}

namespace App{
/**
 * App\UserPosition
 *
 * @property int $id
 * @property int $user_id Nguời dùng
 * @property int $position_id Nguời dùng
 * @property int $created_by Người tạo
 * @property int $updated_by Người cập nhật
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserPosition whereUserId($value)
 */
	class UserPosition extends \Eloquent {}
}

namespace App{
/**
 * App\GroupRole
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property string $icon_name icon cho aside
 * @property int $index vị trí thứ tự
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereIconName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GroupRole whereUpdatedAt($value)
 */
	class GroupRole extends \Eloquent {}
}

namespace App{
/**
 * App\UserRole
 *
 * @property int $id
 * @property int $user_id Nguời dùng
 * @property int $role_id Quyền
 * @property int $created_by Người tạo
 * @property int $updated_by Người cập nhật
 * @property string $created_date Ngày tạo
 * @property string|null $updated_date Ngày cập nhật
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereUpdatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserRole whereUserId($value)
 */
	class UserRole extends \Eloquent {}
}

namespace App{
/**
 * App\Voucher
 *
 * @property int $id
 * @property string $code Mã
 * @property string $name Tên
 * @property string|null $description Mô tả
 * @property int $active Kích hoạt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereUpdatedAt($value)
 */
	class Voucher extends \Eloquent {}
}

