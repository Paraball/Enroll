<?php
require_once 'db.php';

function get_counties()
{
    $sql = "SELECT DISTINCT county FROM districts;";
    $result = query($sql);
    $counties = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $counties[] = $row["county"];
    }
    return $counties;
}

function has_district($county, $district)
{
    if (!is_numeric($district)) {
        return false;
    }
    $sql = "SELECT * FROM districts "
        . "WHERE county='$county' AND district=$district "
        . "LIMIT 1;";
    $result = query($sql);
    return mysqli_num_rows($result) > 0;
}

function get_districts($county)
{
    $sql = "SELECT name FROM districts "
        . "WHERE county='$county' "
        . "ORDER BY district ASC;";
    $result = query($sql);
    $districts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $districts[] = $row["name"];
    }
    return $districts;
}

function get_district_name($county, $district)
{
    $sql = "SELECT name FROM districts "
        . "WHERE county='$county' AND district='$district' "
        . "LIMIT 1;";
    $result = query($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row["name"];
    }
    return null;
}

class Candidate
{

    public $id;
    public $name;
    public $county;
    public $district;
    public $party;

    public function __construct($id, $name, $county, $district = null, $party = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->county = $county;
        $this->district = $district;
        $this->party = $party;
    }

    public static function get_candidate($cand_id)
    {
        $sql = "SELECT name, county, district, party FROM council_candidates "
            . "WHERE id='$cand_id' "
            . "LIMIT 1;";
        $result = query($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            return new Candidate($cand_id, $row["name"], $row["county"], $row["district"], $row["party"]);
        }
        return null;
    }

    public static function get_candidates($county, $district = null, $mode = 'standard')
    {
        //Council candidates
        if ($district) {
            $candidates = array();

            //Name and id only
            if ($mode == 'simple') {
                $result = query("SELECT name, id "
                    . "FROM council_candidates "
                    . "WHERE county='$county' AND district=$district "
                    . "ORDER BY name ASC;");
                while ($row = mysqli_fetch_assoc($result)) {
                    $candidates[$row['id']] = $row['name'];
                }
            }

            //Candidate object
            else if ($mode == 'standard') {
                $result = query("SELECT name, party, id "
                    . "FROM council_candidates "
                    . "WHERE county='$county' AND district='$district' "
                    . "ORDER BY name ASC;");
                while ($row = mysqli_fetch_assoc($result)) {
                    $candidates[$row["id"]] = new Candidate($row["id"], $row["name"], $county, $district, $row["party"]);
                }
            }

            return $candidates;
        }

        //TODO Mayor candidates
    }

}