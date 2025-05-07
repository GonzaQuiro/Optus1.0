<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\User;
use App\Models\CustomerCompany;
use App\Models\OffererCompanyStatus;
use App\Models\Area;
use App\Models\Alcance;

class OffererCompany extends Model
{
    use SoftDeletes;

    protected $table = 'offerer_companies';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'status_id',
        'creator_id',
        'business_name',
        'cuit',
        'postal_code',
        'country',
        'province',
        'city',
        'address',
        'latitude',
        'longitude',
        'first_name',
        'last_name',
        'phone',
        'cellphone',
        'email',
        'website',
        'supplier_code',
        'comments',
        'foundationyear',
        'numberofemployees',
        'annualincome',
        'facebookaccount',
        'twitteraccount',
        'linkedinaccount',
        'companydescription',
        'companyclassification',
        'economicsector',
        'companylogo',
        'certifications'
    ];

    protected $appends = [
        'full_name',
        'logo'
    ];

    public function users()
    {
    	return $this->hasMany(User::class, 'offerer_company_id', 'id');
    }

    public function associated_customers()
    {
    	return $this->belongsToMany(CustomerCompany::class, 'offerers_customers', 'offerer_id', 'customer_id');
    }

    public function areas()
    {
    	return $this->belongsToMany(Area::class, 'offerers_areas', 'offerer_id');
    }

    public function created_by()
    {
    	return $this->hasOne(User::class, 'id', 'creator_id');
    }

    public function status()
    {
    	return $this->hasOne(OffererCompanyStatus::class, 'id', 'status_id');
    }

    public function alcances()
    {
    	return $this->hasMany(Alcance::class, 'id_empresa_oferente', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getLogoAttribute()
    {
        return 
            $this->attributes['companylogo'] && file_exists(rootPath() . filePath(config('app.images_cliente_path') . $this->attributes['companylogo'])) ? 
            $this->attributes['companylogo'] : 
            null;
    }

    public static function getList()
    {
        $result = [];
        if(user()->is_admin){
            $companies = self::all();
        }else{
            $companies = user()->customer_company->associated_offerers;
        }
        

        foreach ($companies as $company) {
            $result[] = [
                'id'            => (string) $company->id,
                'text'          => $company->business_name,
                'is_offerer'    => true,
                'is_customer'   => false
            ];
        }

        return $result;
    }

    public static function getListOptionsNumberOfEmployees()
    {
         return 
        [
            ['id' => '0-10', 'text' => '0-10'],
            ['id' => '11-25', 'text' => '11-25'],
            ['id' => '26-50', 'text' => '26-50'],
            ['id' => '51-100', 'text' => '51-100'],
            ['id' => '101-250', 'text' => '101-250'],
            ['id' => '251-500', 'text' => '251-500'],
            ['id' => 'Más de 500', 'text' => 'Más de 500']
            
        ];
    }

    public static function getListOptionsAnnualIncome()
    {
         return 
         [
            ['id' => 'Menor a 50.000', 'text' => 'Menor a 50.000'],
            ['id' => 'Entre 50.000 y 100.000', 'text' => 'Entre 50.000 y 100.000'],
            ['id' => 'Entre 100.000 y 300.000', 'text' => 'Entre 100.000 y 300.000'],
            ['id' => 'Entre 300.000 y 500.000', 'text' => 'Entre 300.000 y 500.000'],
            ['id' => 'Entre 500.000 y 1.000.000', 'text' => 'Entre 500.000 y 1.000.000'],
            ['id' => 'Más de 1.000.000', 'text' => 'Más de 1.000.000']
        ];
    }

    public static function getListOptionsClassification()
    {
        return 
            [
            ['id' => 'Pequeña', 'text' => 'Pequeña'],
            ['id' => 'Mediana', 'text' => 'Mediana'],
            ['id' => 'Grande', 'text' => 'Grande']
        ];
    }

    public static function getListOptionsEconomicsector()
    {
         return 
         [
            ['id' => 'Público', 'text' => 'Público'],
            ['id' => 'Privado', 'text' => 'Privado'],
            ['id' => 'Ambos', 'text' => 'Ambos']   
        ];
    }
}