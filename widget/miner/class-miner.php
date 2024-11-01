<?php

class WP_Coin_Hive_Widget_Miner extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'monero-miner-for-coin-hive',  // Base ID
            'Monero Miner'   // Name
        );
    }

    public $args = array(
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget' => '</div></div>'
    );

    public function widget($args, $instance)
    {
        global $templateService;

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $text = $instance['text'];
        $context = array(
            'text' => $text
        );

        echo $templateService->loadTemplate('widget-miner')->render($context);

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('', 'text_domain');
        $text = !empty($instance['text']) ? $instance['text'] : esc_html__('', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>">Text:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['text'] = (!empty($new_instance['text'])) ? $new_instance['text'] : '';

        return $instance;
    }
}

?>
