<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees".
 *
 * @property int $employeeNumber
 * @property string $lastName
 * @property string $firstName
 * @property string $extension
 * @property string $email
 * @property string $officeCode
 * @property int $reportsTo
 * @property string $jobTitle
 */
class Employees extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employeeNumber', 'lastName', 'firstName', 'extension', 'email', 'officeCode', 'jobTitle'], 'required'],
            [['employeeNumber', 'reportsTo'], 'integer'],
            [['lastName', 'firstName', 'jobTitle'], 'string', 'max' => 50],
            [['extension', 'officeCode'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 100],
            [['employeeNumber'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employeeNumber' => 'Employee Number',
            'lastName' => 'Last Name',
            'firstName' => 'First Name',
            'extension' => 'Extension',
            'email' => 'Email',
            'officeCode' => 'Office Code',
            'reportsTo' => 'Reports To',
            'jobTitle' => 'Job Title',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\EmployeesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\EmployeesQuery(get_called_class());
    }
}
