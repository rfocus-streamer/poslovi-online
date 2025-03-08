# poslovi-online
poslovi-online/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Service.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── Transaction.php
│   │   ├── Review.php
│   │   ├── Dispute.php
│   │   ├── Affiliate.php
│   │   └── Subscription.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SellerController.php
│   │   │   ├── BuyerController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   │       └── CheckSellerSubscription.php
├── database/
│   ├── migrations/
│   │   ├── 2023_01_01_create_users_table.php
│   │   ├── 2023_01_02_create_categories_table.php
│   │   └── ... (ostale migracije)
├── resources/
│   ├── views/
│   │   ├── seller/
│   │   ├── buyer/
│   │   └── admin/
└── ...
