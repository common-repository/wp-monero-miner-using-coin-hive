<?php

class ThrottleService {

    public function numericToPercentage($throttleNumeric) {
        return (1 - $throttleNumeric)*100;
    }

    public function percentageToNumeric($throttlePercentage) {
        return (100 - $throttlePercentage)/100;
    }
}

?>