<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Salon;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 実店舗（サロン型4店舗）
        $ginza = Salon::create([
            'name' => '銀座本店（Flagship Salon）',
            'slug' => 'ginza',
            'postal_code' => '104-0061',
            'address' => '東京都中央区銀座',
            'phone' => '03-1234-5678',
            'description' => '約25坪 / オンライン配信スタジオ併設・全ラインナップ展示',
            'latitude' => 35.6712,
            'longitude' => 139.7650,
        ]);

        Salon::create([
            'name' => '横浜サロン（Yokohama Salon）',
            'slug' => 'yokohama',
            'postal_code' => '220-0012',
            'address' => '神奈川県横浜市西区みなとみらい',
            'phone' => '045-123-4567',
            'description' => '約15坪 / 首都圏南部拠点',
            'latitude' => 35.4570,
            'longitude' => 139.6322,
        ]);

        Salon::create([
            'name' => '名古屋サロン（Nagoya Salon）',
            'slug' => 'nagoya',
            'postal_code' => '460-0008',
            'address' => '愛知県名古屋市中区栄',
            'phone' => '052-123-4567',
            'description' => '約18坪 / 個室カウンセリングブース2席',
            'latitude' => 35.1681,
            'longitude' => 136.9066,
        ]);

        Salon::create([
            'name' => '大阪心斎橋サロン（Osaka Salon）',
            'slug' => 'osaka',
            'postal_code' => '542-0085',
            'address' => '大阪府大阪市中央区心斎橋筋',
            'phone' => '06-1234-5678',
            'description' => '約20坪 / 関西エリア拠点・鑑別士常駐',
            'latitude' => 34.6711,
            'longitude' => 135.5014,
        ]);

        // 2. 管理者アカウント発行 (初期PW: #12c124j)
        Admin::create([
            'admin_code' => 'clari_jewel_no01',
            'name' => '銀座店統括管理者',
            'password' => Hash::make('#12c124j'),
            'salon_id' => $ginza->id,
        ]);

        // 3. テスト一般ユーザー
        User::create([
            'name' => 'テスト会員',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'rank' => 'regular',
            'total_spent' => 0,
        ]);

        // 4. 初期デモ商品データ（Unsplash画像連携）
        Product::create([
            'title' => 'エテルノ ソリテール ダイヤモンド リング',
            'sku' => 'RING-DIA-001',
            'description' => '透明度クラリティVVS1の最高級ダイヤモンドを使用。シャープな直線エッジが輝きを引き立てます。',
            'price' => 380000,
            'stock' => 3,
            'stock_threshold' => 2,
            'item_category' => 'リング',
            'scene' => 'ブライダル・アニバーサリー',
            'gem_type' => 'ダイヤモンド',
            'metal' => 'プラチナ(Pt950/Pt900)',
            'birth_month' => 4,
            'is_vip_only' => false,
            'has_certificate' => true,
            'is_resizable' => true,
            'images_json' => json_encode(['https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=800']),
        ]);

        Product::create([
            'title' => 'ロイヤルブルー サファイア ステートメント ペンダント',
            'sku' => 'NECK-SAP-001',
            'description' => '深い深青を誇る非加熱サファイア。VIP会員限定の特別提供作品。',
            'price' => 620000,
            'stock' => 1,
            'stock_threshold' => 1,
            'item_category' => 'ネックレス・ペンダント',
            'scene' => 'ハイジュエリー・ステートメント',
            'gem_type' => 'サファイア',
            'metal' => 'ホワイトゴールド(K18WG)',
            'birth_month' => 9,
            'is_vip_only' => true,
            'has_certificate' => true,
            'is_resizable' => false,
            'images_json' => json_encode(['https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=800']),
        ]);
    }
}