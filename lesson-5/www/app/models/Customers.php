<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property int $customerNumber
 * @property string $customerName
 * @property string $contactLastName
 * @property string $contactFirstName
 * @property string $phone
 * @property string $addressLine1
 * @property string $addressLine2
 * @property string $city
 * @property string $state
 * @property string $postalCode
 * @property string $country
 * @property int $salesRepEmployeeNumber
 * @property double $creditLimit
 */
class Customers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customerNumber', 'customerName', 'contactLastName', 'contactFirstName', 'phone', 'addressLine1', 'city', 'country'], 'required'],
            [['customerNumber', 'salesRepEmployeeNumber'], 'integer'],
            [['creditLimit'], 'number'],
            [['customerName', 'contactLastName', 'contactFirstName', 'phone', 'addressLine1', 'addressLine2', 'city', 'state', 'country'], 'string', 'max' => 50],
            [['postalCode'], 'string', 'max' => 15],
            [['customerNumber'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customerNumber' => 'Customer Number',
            'customerName' => 'Customer Name',
            'contactLastName' => 'Contact Last Name',
            'contactFirstName' => 'Contact First Name',
            'phone' => 'Phone',
            'addressLine1' => 'Address Line1',
            'addressLine2' => 'Address Line2',
            'city' => 'City',
            'state' => 'State',
            'postalCode' => 'Postal Code',
            'country' => 'Country',
            'salesRepEmployeeNumber' => 'Sales Rep Employee Number',
            'creditLimit' => 'Credit Limit',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\CustomersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CustomersQuery(get_called_class());
    }
}
