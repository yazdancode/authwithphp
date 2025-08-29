<?php
use Kavenegar\KavenegarApi;

$sender = "2000660110";
$receptor = "";
$message = "وب سرویس پیام کوتاه کاوه نگار";

// ایجاد شیء API با API Key
$api = new KavenegarApi("625558326450733450376B5879782F3345536163717636353759574E58424A304E4735594E7541396235343D");

// ارسال پیامک
$result = $api->Send($receptor, $message, $sender);

if ($result) {
    echo "پیامک با موفقیت ارسال شد!";
} else {
    echo "ارسال پیامک با مشکل مواجه شد!";
}
