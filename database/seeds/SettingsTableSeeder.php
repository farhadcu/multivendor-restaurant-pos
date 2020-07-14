<?php

use Illuminate\Database\Seeder;
use Modules\Admin\Entities\Setting;

class SettingsTableSeeder extends Seeder
{
    protected $settings = [
        //site general details
        [
            'key'                       =>  'site_name',
            'value'                     =>  'KY LIFE',
        ],
        [
            'key'                       =>  'site_title',
            'value'                     =>  'KY LIFE',
        ],
        [
            'key'                       =>  'default_email_address',
            'value'                     =>  'kylife.bd@gmail.com',
        ],
        [
            'key'                       =>  'currency_code',
            'value'                     =>  'BDT',
        ],
        [
            'key'                       =>  'currency_symbol',
            'value'                     =>  'BDT',
        ],
        [
            'key'                       =>  'vat',
            'value'                     =>  '',
        ],

        //site logo & favicon

        [
            'key'                       =>  'site_logo',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'site_favicon',
            'value'                     =>  '',
        ],

        //footer & seo details
        [
            'key'                       =>  'footer_copyright_text',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'seo_meta_title',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'seo_meta_description',
            'value'                     =>  '',
        ],

       
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert($this->settings);
    }
}
