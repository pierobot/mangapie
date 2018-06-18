<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NameCleaner extends TestCase
{
    /**
     * Asserts whether or not the clean function will return Name.
     */
    public function testClean()
    {
        $arrayName = [
            'Name',

            'Name (Digital) (person-Group)',
            'Name (2018) (Digital) (person-Group)',
            'Name (2018) (v01-10) (Digital) (person-Group)',
            'Name (2018) (vol.01-10) (Digital) (person-Group)',

            'Name [Digital] [person-Group]',
            'Name [2018] [Digital] [person-Group]',
            'Name [2018] [v01-10] [Digital] [person-Group]',
            'Name [2018] [vol.01-10] [Digital] [person-Group]',

            'Name_(Digital)_(person-Group)',
            'Name_(2018)_(Digital)_(person-Group)',
            'Name_(2018)_(v01-10)_(Digital)_(person-Group)',
            'Name_(2018)_(vol.01-10)_(Digital)_(person-Group)',

            'Name_[Digital]_[person-Group]',
            'Name_[2018]_[Digital]_[person-Group]',
            'Name_[2018]_[v01-10]_[Digital]_[person-Group]',
            'Name_[2018]_[vol.01-10]_[Digital]_[person-Group]',

            '(Group) Name',
            '(Group) Name (Complete)',
            '(Group) Name (2018) (Complete)',
            '(Group) Name (v01-10) (2018) (Complete)',
            '(Group) Name (vol.01-10) (2018) (Complete)',

            '(Group)_Name',
            '(Group)_Name_(Complete)',
            '(Group)_Name_(2018)_(Complete)',
            '(Group)_Name_(v01-10)_(2018)_(Complete)',
            '(Group)_Name_(vol.01-10)_(2018)_(Complete)',

            '[Group] Name',
            '[Group] Name [Complete]',
            '[Group] Name [2018] [Complete]',
            '[Group] Name [v01-10] [2018] [Complete]',
            '[Group] Name [vol.01-10] [2018] [Complete]',

            '[Group]_Name',
            '[Group]_Name_[Complete]',
            '[Group]_Name_[2018]_[Complete]',
            '[Group]_Name_[v01-10]_[2018]_[Complete]',
            '[Group]_Name_[vol.01-10]_[2018]_[Complete]',
        ];

        foreach ($arrayName as $name) {
            $this->assertEquals('Name', \App\Library::clean($name));
        }
    }

    /**
     * Asserts whether or not the clean function will return A Very Long Name.
     */
    public function testCleanLongName()
    {
        $longNameArray = [
            'A Very Long Name',

            'A Very Long Name (Digital) (person-Group)',
            'A Very Long Name (2018) (Digital) (person-Group)',
            'A Very Long Name (2018) (v01-10) (Digital) (person-Group)',
            'A Very Long Name (2018) (vol.01-10) (Digital) (person-Group)',

            'A Very Long Name [Digital] [person-Group]',
            'A Very Long Name [2018] [Digital] [person-Group]',
            'A Very Long Name [2018] [v01-10] [Digital] [person-Group]',
            'A Very Long Name [2018] [vol.01-10] [Digital] [person-Group]',

            'A_Very_Long_Name_(Digital)_(person-Group)',
            'A_Very_Long_Name_(2018)_(Digital)_(person-Group)',
            'A_Very_Long_Name_(2018)_(v01-10)_(Digital)_(person-Group)',
            'A_Very_Long_Name_(2018)_(vol.01-10)_(Digital)_(person-Group)',

            'A_Very_Long_Name_[Digital]_[person-Group]',
            'A_Very_Long_Name_[2018]_[Digital]_[person-Group]',
            'A_Very_Long_Name_[2018]_[v01-10]_[Digital]_[person-Group]',
            'A_Very_Long_Name_[2018]_[vol.01-10]_[Digital]_[person-Group]',

            '(Group) A Very Long Name',
            '(Group) A Very Long Name (Complete)',
            '(Group) A Very Long Name (2018) (Complete)',
            '(Group) A Very Long Name (v01-10) (2018) (Complete)',
            '(Group) A Very Long Name (vol.01-10) (2018) (Complete)',

            '(Group)_A_Very_Long_Name_',
            '(Group)_A_Very_Long_Name_(Complete)',
            '(Group)_A_Very_Long_Name_(2018)_(Complete)',
            '(Group)_A_Very_Long_Name_(v01-10)_(2018)_(Complete)',
            '(Group)_A_Very_Long_Name_(vol.01-10)_(2018)_(Complete)',

            '[Group] A Very Long Name',
            '[Group] A Very Long Name [Complete]',
            '[Group] A Very Long Name [2018] [Complete]',
            '[Group] A Very Long Name [v01-10] [2018] [Complete]',
            '[Group] A Very Long Name [vol.01-10] [2018] [Complete]',

            '[Group]_A_Very_Long_Name_',
            '[Group]_A_Very_Long_Name_[Complete]',
            '[Group]_A_Very_Long_Name_[2018]_[Complete]',
            '[Group]_A_Very_Long_Name_[v01-10]_[2018]_[Complete]',
            '[Group]_A_Very_Long_Name_[vol.01-10]_[2018]_[Complete]',
        ];

        foreach ($longNameArray as $name) {
            $this->assertEquals('A Very Long Name', \App\Library::clean($name));
        }
    }
}
