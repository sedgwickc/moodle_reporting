<?php

class reward_level {

    private $level;

    public function find_user_level($user){
        if( empty($user) ){
            return null;
        }
        $user_custom_fields = profile_user_record($user->id);
        if( empty( $user_custom_fields->Position ) ) {
            $position = 'None Set';
        } else {
            $position = $user_custom_fields->Position;
        }

        $level_one = array( 'production associate', 
            'officer', 
            'assistants', 
            'inventory clerk');
        $level_two = array( 'cell leader', 
            'associates', 
            'coordinator', 
            'service rep', 
            'sales rep', 
            'technician', 
            'executive assistant', 
            'estimator');
        $level_three = array('team lead', 
            'supervisor', 
            'assistant manager');
        $level_four = array('manager');

        if( in_array( $position, $level_one ) ){
            return 1;
        } elseif ( in_array( $position, $level_two ) ){
            return 2;
        } elseif ( in_array( $position, $level_three ) ){
            return 3;
        } elseif ( in_array( $position, $level_four ) ){
            return 4;
        } else {
            return null;
        }

    }

    public function get_belt_for_level( $mand = 0, $dev = 0, $level = 1) {
        if( $mand < 2 ) {
            return 'none';
        }
        
        switch ($level) {
            case 1:
                if( $mand == 2 ){
                    if( $dev > 1 ){
                        return 'yellow';
                    }
                    return 'white';
                }elseif( $mand == 3 && $dev > 1){
                    return 'green';
                } elseif ($mand >= 4 && $dev > 2 ){
                    return 'blue';
                } else {
                    return 'none';
                }
                break;
            case 2:
                if( $mand == 2 ){
                    return 'white';
                } elseif ( $mand == 3 && $dev > 1 ){
                    return 'yellow';
                }elseif( $mand >= 4 && $dev == 2){
                    return 'green';
                } elseif ($mand >= 4 && $dev >= 3 ){
                    return 'blue';
                } else {
                    return 'none';
                }
            case 3:
                if( $mand == 3 && $dev >= 1 ){
                    return 'white';
                } elseif ( $mand == 4 && $dev == 2 ){
                    return 'yellow';
                }elseif( $mand == 4 && $dev == 3 ){
                    return 'green';
                } elseif ($mand >= 4 && $dev >= 4 ){
                    return 'blue';
                } else {
                    return 'none';
                }
            case 4:
                if( $mand == 4 && $dev == 2 ){
                    return 'white';
                } elseif ( $mand == 4 && $dev == 3 ){
                    return 'yellow';
                }elseif( $mand == 4 && $dev == 4 ){
                    return 'green';
                } elseif ($mand >= 4 && $dev >= 5 ){
                    return 'blue';
                } else {
                    return 'none';
                }
            }
    }

    public function get_level() {
        return $this->level;
    }

}
