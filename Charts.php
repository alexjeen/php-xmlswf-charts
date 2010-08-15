<?php  

/**
 * Holds a wrapper for XML / SWF charts from Maani.us
 * 
 * Methods with more than 5 parameters take a single array as argument,
 * for example chart_guide, parameters not mentioned in the format key => value
 * will not be included in the xml tag
 * 
 * Also defaults are not mentioned in this class, because defaults mostly depend
 * on the type of graph
 *
 * @author Alex Jeensma
 * @company http://vontis.nl
 * @copyright 2010
 * @date 13-8-2010
 * @time 0:24
 */
 
class Charts
{
    /**
     *  Holds the SimpleXML representation of this chart
     */ 
    private $xml = null;
    
    /**
     *  If you have a license put it here
     */ 
    private $license = 'ITQ6VK4RRU1O94Z0B6SVMYWHM5SXBL';
    
    /**
     *  Chart data (in XML)
     */ 
    private $chart_data = null;
    
    /**
     * Charts::Charts()
     * 
     * Creates a new instance of the charts library
     * 
     * @return void
     */
    public function Charts()
    {   
        $this->xml = new SimpleXMLElement('<?xml version="1.0" ?><chart><chart_data></chart_data></chart>');
        
        $this->chart_data = $this->xml->chart_data;
        $this->chart_type = $this->xml->chart_type;
        
        if($this->license) {
            $this->xml->license = $this->license;
        }
    }
    
    /**
     * Charts::getXML()
     * 
     * Gets the generated XML
     * 
     * @return
     */
    public function getXML()
    {
        return $this->xml->asXML();
    }
    
    /**
     * Charts::set_type()
     * 
     * In standard charts, only one chart type is specified in a single string.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_type
     * 
     * @param mixed $type
     * 
     * @return void
     */
    public function set_type($type)
    {
        $this->xml->addChild('chart_type', $type);
    }
    
    /**
     * Charts::set_types()
     * 
     * In mixed charts, more than one chart type are specified in multiple strings. The first chart type is applied to the first row of data (first series), the second chart type is applied to the second row of data (second series), and so on.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_type
     * 
     * @param mixed $types
     * 
     * @return void
     */
    public function set_types($types)
    {
        $chart_type = $this->xml->addChild('chart_type');
        
        foreach($types as $type)
        {
            $chart_type->addChild('string', $type);
        }
    }
    
    /**
     * Charts::add_row()

     * Adds a row to the table, if it can be parsed as a number 
     * it will get the datatype number, otherwise string
     * 
     * @param array $row array you want to pass on, also include null for consistency
     * @param array $attributes the attributes you want to add to the number tags
     * 
     * @return void
     */
    public function add_row($row, $attributes = array())
    {
        $row_child = $this->chart_data->addChild('row');
        
        foreach($row as $key => $value)
        {
            $parsed = $this->_parse_value($value);
            
            $added_child = $row_child->addChild($parsed, $parsed != 'null' ? $value : '');
            
            if(key_exists($key, $attributes) && is_array($attributes[$key])) {
                foreach($attributes[$key] as $attr => $val) {
                    $added_child->addAttribute($attr, $val);
                }
            }
        }
    }
    
    /**
     * Charts::add_rows()
     * 
     * Adds multiple rows with add_row
     * 
     * @param mixed $rows
     * @param mixed $attributes
     * 
     * @return void
     */
    public function add_rows($rows, $attributes = array())
    {
        foreach($rows as $key => $row)
        {
            $this->add_row($row, key_exists($key, $attributes) ? $attributes[$key] : array());
        }
    }
    
    /**
     * Charts::_parse_value()
     * 
     * Parses a value and returns it type, null string or number
     * 
     * @param mixed $val Value you want to parse
     * 
     * @return number null or string
     */
    private function _parse_value($val)
    {
        if(is_null($val)) {
            return 'null';
        }
        if(preg_match('|^[A-z0-9]{6}$|', $val) > 0) {
            return 'color';
        }
        if(is_numeric($val)) {
            return 'number';
        }
        return 'string';
    }
 
    /**
     * Charts::border()
     * 
     * chart_border sets the chart's border attributes.
     * 
     * @see http://www.maani.us/xml_charts/index.php?menu=Reference&submenu=chart_border
     * 
     * @param mixed $top_thickness
     * @param mixed $bottom_thickness
     * @param mixed $left_thickness
     * @param mixed $right_thickness
     * @param mixed $color
     * 
     * @return void
     */
    public function border($top_thickness, $bottom_thickness, $left_thickness, $right_thickness, $color)
    {
        $this->_add_attribute_node('chart_border', array(
            'top_thickness' => $top_thickness, 
            'bottom_thickness' => $bottom_thickness,
            'left_thickness' => $left_thickness,
            'right_thickness' => $right_thickness,
            'color' => $color
        ));
    }
    
    /**
     * Charts::grid_h()
     * 
     * chart_grid_h sets the chart's horizontal grid attributes.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_grid_h
     * 
     * @param mixed $thickness
     * @param mixed $color
     * @param mixed $alpha
     * @param mixed $type
     * 
     * @return void
     */
    public function grid_h($thickness, $color, $alpha, $type)
    {
        $this->_add_attribute_node('chart_grid_h', array(
            'thickness' => $thickness, 
            'color' => $color,
            'alpha' => $alpha,
            'type' => $type
        ));        
    }
    
    /**
     * Charts::grid_v()
     * 
     * chart_grid_v sets the chart's vertical grid attributes.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_grid_v
     * 
     * @param mixed $thickness
     * @param mixed $color
     * @param mixed $alpha
     * @param mixed $type
     * @return void
     */
    public function grid_v($thickness, $color, $alpha, $type)
    {
        $this->_add_attribute_node('chart_grid_v', array(
            'thickness' => $thickness, 
            'color' => $color,
            'alpha' => $alpha,
            'type' => $type
        ));          
    }
    
    /**
     * Charts::guide()
     * 
     * chart_guide sets one or two guide lines to connect the cursor position with the axes and simplify reading their values. 
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_guide
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function guide($parameters)
    {
        $this->_add_attribute_node('chart_guide', $parameters);         
    }
    
    /**
     * Charts::label()
     * 
     * chart_label sets the attributes of the labels that appear over the graphs.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_label
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function label($parameters)
    {
        $this->_add_attribute_node('chart_label', $parameters); 
    }
    
    /**
     * Charts::note()
     * 
     * chart_note defines the look of comments that can be attached to data or category points. They can be added with chart_data. They are supported by all chart types except for 3d, image, bubble, and mixed charts
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_note
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function note($parameters)
    {
        $this->_add_attribute_node('chart_note', $parameters); 
    }
    
    /**
     * Charts::pref()
     * 
     * chart_pref sets the preferences for some charts. Each chart type has different preferences, or no preferences at all.
     *
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_pref 
     *
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function pref($parameters)
    {
        $this->_add_attribute_node('chart_pref', $parameters); 
    }
    
    /**
     * Charts::rect()
     * 
     * chart_rect sets the chart's rectangle.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_rect
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function rect($parameters)
    {
        $this->_add_attribute_node('chart_rect', $parameters); 
    }
    
    /**
     * Charts::transition()
     * 
     * chart_transition sets the chart's transition attributes:
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=chart_transition
     * 
     * @param mixed $type
     * @param mixed $delay
     * @param mixed $duration
     * @param mixed $order
     * 
     * @return void
     */
    public function transition($type, $delay, $duration, $order)
    {
        $this->_add_attribute_node('chart_transition', 
            array(
                'type' => $type,
                'delay' => $delay,
                'duration' => $duration,
                'order' => $order
            )
        ); 
    }
    
    /**
     * Charts::series()
     * 
     * series determines the look of the series graphs of some chart types.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=series
     * 
     * @param mixed $bar_gap
     * @param mixed $set_gap
     * @param mixed $transfer
     * 
     * @return void
     */
    public function series($bar_gap, $set_gap, $transfer)
    {
        $this->_add_attribute_node('series',
            array(
                'bar_gap' => $bar_gap,
                'set_gap' => $set_gap,
                'transer' => $transfer
            )
        );
    }
    
    /**
     * Charts::series_color()
     * 
     * series_color sets the colors to use for the chart series.
     * 
     * http://maani.us/xml_charts/index.php?menu=Reference&submenu=series_color
     * 
     * @param mixed $colors only specify valid hex colors like 000000 or ffffff
     * 
     * @return void
     */
    public function series_color($colors = array())
    {
        $this->_add_node('series_color', $colors);
    }
    
    /**
     * Charts::series_explode()
     * 
     * series_explode applies to pie, line, and scatter charts only. In pie charts, it sets which pie slice separates from the pie for emphasis. In line and scatter charts, it sets which line or point is increased in thickness or size for emphasis.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=series_explode
     * 
     * @param mixed $numbers
     * 
     * @return void
     */
    public function series_explode($numbers = array())
    {
        $this->_add_node('series_explode', $numbers);
    }
    
    /**
     * Charts::axis_category()
     * 
     * axis_category sets the label attributes for the category-axis.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=axis_category
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function axis_category($parameters = array())
    {
        $this->_add_attribute_node('axis_category', $parameters);
    }
    
    /**
     * Charts::axis_category_label()
     * 
     * axis_category_label is an array that overrides the numeric, default axis_category labels in scatter and bubble charts. 
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=axis_category_label
     * 
     * @param mixed $labels
     * 
     * @return void
     */
    public function axis_category_label($labels = array())
    {
        $this->_add_node('axis_category_label', $labels);
    }
    
    /**
     * Charts::axis_ticks()
     * 
     * axis_ticks sets the tick marks on the chart axes.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=axis_ticks
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function axis_ticks($parameters = array())
    {
        $this->_add_attribute_node('axis_ticks', $parameters);
    }
    
    /**
     * Charts::axis_value()
     * 
     * axis_value sets the label attributes for the value-axis.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=axis_value
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function axis_value($parameters = array())
    {
        $this->_add_attribute_node('axis_value', $parameters);
    }
    
    /**
     * Charts::axis_value_label()
     * 
     * axis_value_label is an array that overrides the default axis_value labels to display custom text instead. This can also be used to reverse the axis_value
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=axis_value_label
     * 
     * @param mixed $values
     * 
     * @return void
     */
    public function axis_value_label($values = array())
    {
        $this->_add_node('axis_value_label', $values);
    }
    
    /**
     * Charts::draw()
     * 
     * draw holds any number of elements to draw. A draw element can be a circle, image (JPEG, unanimated GIF, PNG, or SWF), line , rect, or text.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=draw
     * 
     * @param mixed $shape shape to draw
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function draw($shape, $parameters = array())
    {
        $this->_add_to_child('draw', $shape, $parameters);
    }
    
    /**
     * Charts::filter()
     * 
     * filter defines any number of filters to enhance the look of or highlight different graphic elements. Filters can be applied to axis_category, axis_value, legend, chart_data, chart_rect, chart_label, and draw.

After defining filters, one or more can be applied to different chart elements. For example, you can define one shadow and two bevel filters. You can apply just the shadow to one element, just the first bevel to a second element, and both the shadow and the second bevel to a third element.
     *
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=filter
     * 
     * @param mixed $filter filter to apply
     * @param mixed $parameters parameters for the method in key => value format
     * @return void
     */
    public function filter($filter, $parameters = array())
    {
        $this->_add_to_child('filter', $filter, $parameters);
    }
    
    /**
     * Charts::context_menu()
     * 
     * context_menu determines which functions to include in the context menu when the user right-clicks a graph
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=context_menu
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function context_menu($parameters = array())
    {
        $this->_add_attribute_node('context_menu', $parameters);
    }
    
    /**
     * Charts::embed()
     * 
     * embed imports a flash file containing fonts to use in addition to the default Arial font.
     * 
     * @see = http://maani.us/xml_charts/index.php?menu=Reference&submenu=embed
     * 
     * @param mixed $url
     * @param mixed $timeout
     * @param mixed $retry
     * @param mixed $fonts
     * @return void
     */
    public function embed($url, $timeout, $retry, $fonts = array())
    {
        $embed = $this->xml->addChild('embed');
        
        $embed->addAttribute('url', $url);
        $embed->addAttribute('timeout', $timeout);
        $embed->addAttribute('retry', $retry);
        
        foreach($fonts as $font)
        {
            $this->embed->addChild('font', $font);
        }
    }
    
    /**
     * Charts::legend()
     * 
     * legend sets the legend's attributes. The legend is the area that identifies the colors assigned to the graphs. 
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=legend
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function legend($parameters = array())
    {
        $this->_add_attribute_node('legend', $parameters);
    }
    
    /**
     * Charts::link()
     * 
     * link holds any number of areas, each defining a rectangle and a URL to go to when the user clicks inside the rectangle. This can also be used to assign functions to mouse clicks, including chart updates, printing, etc.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=link
     * 
     * @param mixed $areas areas with their attributes in key => val format
     * 
     * @return void
     */
    public function link($areas = array())
    {
        foreach($areas as $area)
        {
            $this->_add_to_child('link', 'area', $area);            
        } 
    }
    
    /**
     * Charts::link_data()
     * 
     * link_data sets the URL of a script responsible for processing clicks on chart elements. This enables drilling down into charts.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=link_data
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function link_data($parameters = array())
    {
        $this->_add_attribute_node('link_data', $parameters);
    }
    
    /**
     * Charts::scroll()
     * 
     * scroll activates and sets the attributes of a slider that makes the chart scrollable. Scrolling is supported by all chart types except for 3d, pie, donut, polar, scatter, bubble, and image charts.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=scroll
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function scroll($parameters = array())
    {
        $this->_add_attribute_node('scroll', $parameters);
    }
    
    /**
     * Charts::tooltip()
     * 
     * tooltip defines the look of the cursor label that appears when the mouse moves over some chart elements. The tooltip is the black on red label that appears when moving the mouse over the chart and the link area here:
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=tooltip
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function tooltip($parameters = array())
    {
        $this->_add_attribute_node('tooltip', $parameters);
    }
    
    /**
     * Charts::update()
     * 
     * update updates the chart without reloading the web page. This makes it possible to display charts with live data, change the chart's look over time for emphasis, or create a slideshow from different charts.
     * 
     * @see http://maani.us/xml_charts/index.php?menu=Reference&submenu=update
     * 
     * @param mixed $parameters parameters for the method in key => value format
     * 
     * @return void
     */
    public function update($parameters = array())
    {
        $this->_add_attribute_node('update', $parameters);
    }
    
    /**
     * Charts::_add_to_child()
     * 
     * @param mixed $child
     * @param mixed $title
     * @param mixed $attributes
     * @return void
     */
    public function _add_to_child($child, $title, $attributes = array())
    {
        $child = ($this->xml->{$child}) ? $this->xml->{$child} : $this->xml->addChild($child);
        
        $attribute = $child->addChild($title);
        
        foreach($attributes as $key => $val)
        {
            if($val) {
                $attribute->addAttribute($key, $val);   
            }
        }        
    }
    
    /**
     * Charts::_add_attribute_node()
     * 
     * Adds a attribute node to the chart node
     * 
     * @param mixed $title title of the node
     * @param mixed $attributes attributes
     * 
     * @return void
     */
    private function _add_attribute_node($title, $attributes = array())
    {
        $attribute = $this->xml->addChild($title);
        
        foreach($attributes as $key => $val)
        {
            if(is_bool($val)) {
                $val = ($val == TRUE) ? 'true' : 'false';
            }
            
            if($val) {
                $attribute->addAttribute($key, $val);   
            }
        }
    }
    
    /**
     * Charts::_add_node()
     * 
     * @param mixed $title
     * @param mixed $values
     * @return void
     */
    private function _add_node($title, $values = array())
    {
        $node = $this->xml->addChild($title);
        
        foreach($values as $value)
        {
            $parsed = $this->_parse_value($value);
            
            $node->addChild($parsed, $parsed != 'null' ? $value : '');
        }
    }
}

/* End of file charts.php */