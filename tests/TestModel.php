<?php

use Sofiakb\Database\Tests\Models\Contact;

/**
 * This file contains TestModel class.
 * Created by PhpStorm.
 * User: Sofiane Akbly <sofiane.akbly@gmail.com>
 * Date: 29/07/2021
 * Time: 10:51
 */
class TestModel extends \PHPUnit\Framework\TestCase
{
    
    private array $data;
    
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->data = ['name' => 'sofiane', 'age' => 25, 'birthdate' => '1996-12-09', 'gender' => 'M'];
        parent::__construct($name, $data, $dataName);
    }
    
    public function testAllAtOnce()
    {
        $contact = Contact::insert($this->data);
        $this->assert($contact);
        
        $contactWhere = Contact::whereId(1)->first();
        $this->assert($contactWhere);
        
        $contactWhere = Contact::where('age', '>', 1)->first();
        $this->assert($contactWhere);
        
        $this->data['name'] = 'leila';
        Contact::updateBy($this->data, 'name', '=', 'sofiane');
        $contactWhere = Contact::first();
        $this->assert($contactWhere);
        
        $this->data['name'] = 'sofiane';
        Contact::whereName('leila')->update($this->data);
        $contactWhere = Contact::first();
        $this->assert($contactWhere);
        
        Contact::truncate();
    }
    
    private function assert($contact)
    {
        $contact = toObject($contact);
        $this->assertEquals($this->data['name'], $contact->name);
        $this->assertFalse($contact->active);
    }
}