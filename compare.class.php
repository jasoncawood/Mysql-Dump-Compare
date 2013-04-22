<?php
class Compare
{
	public $File1;
	public $File2;
	public $CurrentQuery;
	public $StartLine;
	public $QueryType2;
	private $FileDesc;

	function Compare()
	{
        $this->QueryType2;
		$this->FileDesc;
	}

	function ProcessFile()
	{
		$Start = false;
		$End = false;
		$QueryType = '';
		$QueryToCompare = '';
        $PreviouseQuery = '';
        $FindInsQuery = 0;

		$FArray1 = file($this ->File1);
		$FArray2 = $FArray1;
		//open file for writing query in
		$this->OpenFile();

        //as this is end of query now compare with another query in another file.
        $QueryToCompare = $this -> CompareQuery();
		foreach($FArray1 as $Key=>$Value)
		{

			//check query type
			$QueryType = $this->FindQueryType($Value);
			$this -> QueryType2 = $QueryType;

            //check query type is INSERT QUERY
            $InsQuery = strpos(strtoupper($Value),"INSERT INTO");
            if($InsQuery === false)
            {
                $FindInsQuery = 0;
            }
            else
            {

                if($FindInsQuery == 1)
                {
                    $QueryType = $this -> NextInsQuery($PreviouseQuery, $FArray2);
                }
                else
                {
                    $FindInsQuery = 1;
                }

            }
            //CREATE TABLE
			$pos = strpos(strtoupper($Value),$QueryType);

			if($pos === false)
			{
			}
			else
			{
                $Start = false;
                $End= false;
                $this -> StartLine = '';
                $this -> CurrentQuery = '';
				//$this -> CurrentQuery = $Value;
				$Start = true;
			}
			//echo $End;
			//if find query
			if($Start == true && $End == false)
			{
				$this -> CurrentQuery .= $Value;
                $End = $this -> QueryEnd($Value);
			}

			//on the end of query if find ;
			if($End == true && $Start == true)
			{
				//echo $this -> CurrentQuery."<BR><BR>";
				$PreviouseQuery = $Value;

				$pos = strpos(strtoupper($QueryToCompare),strtoupper($this -> CurrentQuery));

                if($pos === false)
                {
                    echo 'Query not same<br>QTC<br>';
                    echo $QueryToCompare."<br>CQ<br>";
                    echo $this->CurrentQuery."<br>";
                    $this->WriteToFile($this -> CurrentQuery);
                }
                else
                {

                }
                  /*
				if($QueryToCompare == $this -> CurrentQuery)
				{
					echo 'Same Query';

				}
				elseif( $QueryToCompare != $this -> CurrentQuery)
				{
					echo 'Query not same<br>QTC<br>';
                    echo $QueryToCompare."<br>CQ<br>";
                    echo $this->CurrentQuery."<br>";
					$this->WriteToFile($this -> CurrentQuery);
				}
                */

				$Start = false;
				$End= false;
				$this -> StartLine = '';
                $this->QueryType2 = '';
                $this -> CurrentQuery = '';
			}
		}

	}

    function NextInsQuery($Q,$File)
    {
        $FindIns = false;
        $CurrentQuery = '';
        foreach($File as $Key=>$Value)
        {
            $pos = strpos(strtoupper($Value),"INSERT INTO");

            if($pos === false)
            {
            }
            else
            {
                $Start = false;
                $End= false;
                //$this -> CurrentQuery = $Value;
                $Start = true;
            }

            if($Start == true && $End == false)
            {
                $End = $this -> QueryEnd($Value);
            }

            if($End == true && $Start == true)
            {
                if($FindIns == true)
                {
                    $FindIns = false;
                    return strtoupper($Value);
                }

                $pos = strpos(strtoupper($Value),strtoupper($Q));
                if($pos === false)
                {
                }
                else
                {
                    if($FindIns == false)
                    {
                        $FindIns = true;
                    }
                }


            }
        }
    }

	function OpenFile()
	{
		$this->FileDesc = fopen("output.sql","w");
	}

	function WriteToFile($Query)
	{
		fwrite($this->FileDesc,$Query);
	}

	function QueryEnd($Line)
	{

		$Arr = split(';',$Line);

		//if find ; then count will more then 1
		if(count($Arr) > 1)
		{

			if(trim($End) == '')
			{
				//its mean it is end
				// return true

				return true;
			}
			else
			{
				return false;
			}
		}
	}

	//this function will compare query in second file
	function CompareQuery()
	{
		//don't do any thing if query type is empty

            $HoldFile = '';
			$CompFileArr = file($this -> File2);

			foreach($CompFileArr as $Key => $Value)
			{
				//find query in current line of array
                $HoldFile .= $Value;
			}

            return $HoldFile;

	}

	function FindQueryType($QLine)
	{
		$QParts = split(" ",strtoupper(trim($QLine)));

		if($QParts[0]." ".$QParts[1] == "DELETE FROM")
		{
			return $QParts[0]." ".$QParts[1]." ".$QParts[2];
		}
		elseif($QParts[0]." ".$QParts[1] == "CREATE TABLE")
		{
			return $QParts[0]." ".$QParts[1]." ".$QParts[2];
		}
		elseif($QParts[0]." ".$QParts[1] == "INSERT INTO")
		{
			return $QParts[0]." ".$QParts[1]." ".$QParts[2];
		}
		elseif($QParts[0]." ".$QParts[1] == "UPDATE TABLE")
		{
			return $QParts[0]." ".$QParts[1]." ".$QParts[2];
		}

         if(trim($this->QueryType2) !="")
         {
             return $this->QueryType2;
         }
	}
}
?>