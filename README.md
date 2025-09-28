# WHMCS Payriff Payment Gateway Module

Bu repo **WHMCS PAYRIFF GATEWAY** moduludur.  

Faylı WHMCS qurulumunuzda bu qovluğa yerləşdirin:
/modules/gateways/


## Status
- Modul işləkdir və test olunub.
- WHMCS 8.x versiyaları ilə uyğunluq sınaqdan keçirilib.


## İstifadə
1. payriff.php və callback/payriff.php fayllarını yükləyin.
2. yüklədiyiniz payriff.php faylını /modules/gateways/ qovluğuna yükləyin.
3. yüklədiyiniz callback/payriff.php faylını /modules/gateways/callback/ qovluğuna yükləyin
4. callback/payriff.php faylını açın və içərisində göstərilən yerdə whmcs admin panel istifadəçi adınızı daxil edin.
5. WHMCS admin panelinizdə Payment Gateways bölməsinə daxil olun, artıq Payriff modulu burada görünəcək.
6. Payriff moduluna daxil olun (sağda kiçik qələm şəkli ilə)
7. Show on Order Form -  check edin. (bu Payriff modulunun saytınızda görünməsini təmin edəcək.)
8. Display Name - burada istədiyiniz adı daxil edə bilərsiz, bu saytınızda görünən ad olacaq.
9. Merchant ID - burada Payriff hesabınızdakı Merchant ID-nizi daxil edin.
10. Secret Key- burada Payriff hesabınızdakı Secret Key-nizi daxil edin.
11. Convert To For Processing - none seçin.
12. Save Changes edin və artıq modul işləkdir.

## License
MIT License

