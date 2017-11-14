/* Alcohol Calculator
 * @author dan
 */

$(function () 
{
    $('#calculator').show();
    $('#calculator input[type = "text"], .extra-params input[type = "text"]').bind('keyup', function () {
        if ( (/^[0-9,.]+$/).test(this.value) )
            $(this).css('color', '');
        else
            $(this).css('color', 'red');
    })        
})

var Pickers = {}

function onBeverageAdd ()
{
    var $li = window.$list.find('li:last');
    
    if (window.tab == 'beverage')
    {
        //Drink strength
        $li.find('.strong-picker').remove();    
        
        var $picker = $('<div class="strong-picker"></div>');
        $list.find('li:last table td:eq(1)').append($picker);
        
        $picker.picker({unit: '%', decimalLimit: 20});
        $picker.attr('id', 'picker-'+ new Date().getTime());
        Pickers[$picker.attr('id')] = $picker;
    
        var $select = $li.find('select:first');
        $select.attr('for', $picker.attr('id'));
        $select.bind('change', function (e) 
        {        
            setPickerForBeverage(
                Pickers[$(this).attr('for')],
                $(this).find('option:selected') 
            ) 
        })
    
        setPickerForBeverage($picker, $select.find('option:selected'));
        
        $('#calculator input.volume').unbind('blur').bind('blur', function () 
        {
            if (!params.$weight.val()) params.$weight[0].focus();
        })
                
    } 
    else if (window.tab == 'beer') 
    {        
        $li.find('.strong-picker').html('');
        $picker = $('<div class="picker"></div>');        
        $li.find('.strong-picker').append($picker);
    
        $picker.attr('id', 'picker-'+ new Date().getTime());
        Pickers[$picker.attr('id')] = $picker;
    
        var $select = $li.find('select:first');
        $select.attr('for', $picker.attr('id'));
        $select.bind('change', function (e) 
        {
            setPickerForBeer(
                Pickers[$(this).attr('for')], 
                $(this).find('option:selected')
            );
        });                
        setPickerForBeer(
            $picker, 
            $select.find('option:selected')
        );
    }
    
    //Time left picker
    $li.find('.time-picker').remove();    
    
    var $timePicker = $('<div class="time-picker"></div>');
    $list.find('li:last table td:eq(3)').append($timePicker);
    
    $timePicker.picker({
        unit         : s('hrs'), 
        localeUnitKey: 'hour ago',
        zeroUnit     : s('right now'),
        decimalLimit : 1, 
        min          : 0,
        max          : 24            
    });
    $timePicker.setValue(1);
    
    function setPickerForBeer ($picker, $selectedOption)
    {        
        var value = $selectedOption.attr('abv');
        $picker.html(value.number() + '&thinsp;%').attr('value', value);
    }
    
    function setPickerForBeverage ($picker, $selectedOption)
    {
        var min = parseInt($selectedOption.attr('min')),
            max = parseInt($selectedOption.attr('max')),
            abv = parseInt($selectedOption.attr('abv')),
            avg = (max + min) / 2;
        
        avg = min <= 20 ? Math.round(avg * 10) / 10 : Math.round(avg);
        
        $picker.setOptions( 
            {max: max, min: min} 
        );
        $picker.setValue( 
            isNaN(abv) ? avg : abv
        );
    }
    onBeverageChange();       
}

function onBeverageChange ()
{
    var weight = parseInt(window.params.$weight.val().replace(',', '.')),
        sex    = parseInt(window.params.$sex.val()),
        emptyStomack = window.params.$emptyStomack.filter(':checked').length;        
    
    var $result = $('#calculator .result');
    $result.find('.alert').hide();
    
    if (weight < 10) 
    {
        $result.find('.ppm').text('—');
        $result.find('.sober-time').text('—');
        return;
    }
    
    var alcoholVolume = 0,
        alcohol = [],
        hoursLeft = 0; //common time for sober
    
    $('#calculator > div:first li').each(function () 
    {
        var volume = $(this).find('input[name = "volume"]').val().replace(',', '.');
        if (window.tab == 'beer') 
            volume *= 1000;
            
        var strong = $(this).find('.strong-picker .picker').attr('value') / 100,
            ppm = getPromille (strong*volume, weight, sex, emptyStomack),
            timeAgo = parseInt($(this).find('.time-picker .picker').attr('value'));
        
        alcoholVolume += ppm;
        alcohol.push({ppm: ppm, timeAgo: timeAgo});
        
        if (timeAgo > hoursLeft) 
            hoursLeft = timeAgo;
    })            
        
    if (isNaN(alcoholVolume))
    {        
        $result.find('.ppm').text('—');
        $result.find('.sober-time').text('—');
        return;
    }
    
    /* 
        Old method:
        alcoholVolume = Math.round((alcoholVolume - 0.15*period) * 100) / 100;    
     */
    
    /*
        Smart ppm calculation for this moment:
        with decreasing of common time left for sober
     */ 
    var REDUCTION = 0.13; //ppm in hour  
    alcoholVolume = 0;            
    for (var i = 0; i < alcohol.length; i++)
    {   
        var ppm = alcohol[i].timeAgo <= hoursLeft
            ? alcohol[i].ppm - REDUCTION * alcohol[i].timeAgo
            : alcohol[i].ppm - REDUCTION * hoursLeft;
                
        if (ppm < 0) ppm = 0;
        
        hoursLeft -= alcohol[i].ppm / REDUCTION;
        if (hoursLeft < 0) hoursLeft = 0;
            
        alcoholVolume += ppm;
    }     
    /* end: ppm calculation */
       
    alcoholVolume = Math.round(alcoholVolume * 100) / 100;    
    
    if (alcoholVolume < 0) alcoholVolume = 0;
    
    $result.find('.ppm').html(
        alcoholVolume.number() + '&thinsp;‰'
    );
    
    var soberTime = Math.round(alcoholVolume / REDUCTION),
        soberSign = soberTime +' '+ UnitCase('hour', soberTime);
        
    if (soberTime < 1) soberSign = s('less hour');
    else if (!alcoholVolume) soberSign = '—';
        
    var driveTime = Math.round(alcoholVolume / REDUCTION);    
    if (driveTime < 0) driveTime = 0;
    var driveSign = driveTime +' '+ UnitCase('hour', driveTime);            
    
    if (driveTime < 1) driveSign = s('less hour');
    else if (!alcoholVolume) driveSign = '—';
    
    $result.find('.value.sober-time').text(soberSign);
    $result.find('.value.drive-time').text(driveSign).parent().show();
    
    if (alcoholVolume >= 5) { 
        window.alerts.$death.show(); 
        $result.find('.sober-time').text('—');
        $result.find('.value.drive-time').parent().hide();
        return; 
    }    
    if (alcoholVolume >= 3) { 
        window.alerts.$poisoning.show(); 
        return; 
    }
    if (alcoholVolume >= 2)   { window.alerts.$strong.show(); return; }
    if (alcoholVolume >= 1.5) { window.alerts.$middle.show(); return; }
    if (alcoholVolume >= 0.5) { window.alerts.$easy.show(); return; }
    if (alcoholVolume >= 0.3) { window.alerts.$none.show(); return; }    
    if (alcoholVolume == 0)   { 
        $result.find('.sober-time').text('—'); 
        window.alerts.$drive.show();
        $result.find('.value.drive-time').parent().hide();
    }        
}

//Widmark Formula
function getPromille (alcoholVolume, bodyWeight, sex, emptyStomack)
{
    var widmarkCoef = sex ? 0.68 : 0.55,
        foodCoef = emptyStomack ? 0.9 : 0.7;
    
    var value = alcoholVolume / (bodyWeight * widmarkCoef);
    value *= foodCoef;
    
    return value;
}

/*
 * jQuery plugins
 */
var CSS_ADD = {
        position    : 'absolute', 
        left        : '0',
        bottom      : '0',
        fontSize    : '2em',
        color       : '#555',
        cursor      : 'pointer',
        marginLeft  : '-15px',
        marginBottom: '-15px'
    },
    CSS_REMOVE = {
        position    : 'absolute',
        right       : '0',
        top         : '0',
        marginLeft  : '5px',
        color       : 'red',
        fontSize    : '1.4em',
        cursor      : 'pointer',
        marginRight : '-1em'
        
    },
    CSS_LI = {
        position: 'relative',
        float:'left',
        clear:'both'
    },
    CSS_LIST = {
        position: 'relative',
        float:'left'
    }

$.fn.additionalList = function (options)
{    
    var self  = this;    
    var defaults = {
        onCreate: function () {},
        onAdd   : function () {},
        onRemove: function () {},
        onChange: function () {}
    }
    options = $.extend (defaults, options);
    
    // this.css(CSS_LIST);
    
    var $list = this.find('div:first ul');                    
    
    var $add    = $('<span>+</span>').css(CSS_ADD).addClass('add'),
        $remove = $('<span>×</span>').css(CSS_REMOVE).addClass('remove');
    
    // var $li = $list.find('li:last').css(CSS_LI);        
    
    $add.bind('click', addListItem);
    $remove.bind('click', removeListItem);                        
    //$li.append($add).append($remove);        
    
    //Init
    refreshList();
    options.onAdd();
    assignListItemHandlers();    
    
    function refreshList ()
    {
        $list.find('li.last').removeClass('last').find('.add').hide();
        $list.find('li:last').addClass('last').find('.add').show();    
        
        if ($list.find('li').length <= 1)
            $list.find('.remove').css('visibility', 'hidden');
        else
            $list.find('.remove').css('visibility', 'visible');
    }    
    
    function assignListItemHandlers ()
    {
        var $li = $list.find('li:last');
        $li.find('select').bind('change', options.onChange);
        $li.find('input').bind('keyup', options.onChange);
    }
    
    /*
     * Event handlers
     */
    function addListItem ()
    {
        var $newLI = $list.find('li:last').clone(true);        
        $list.append($newLI);
        refreshList();
        options.onAdd();
        assignListItemHandlers();        
    }
    
    function removeListItem ()
    {
        if ($list.find('li').length > 1)
        {
            $(this.parentNode).remove();
            refreshList();
            options.onRemove();
        }        
    }
    
    return this;
}

var CSS_PICKER_AREA = {
        position : 'relative',
        display  : 'inline-block',
        borderTop: 'solid 1px #CCC'
    },
    CSS_PICKER = {
        position  : 'absolute',
        left      : '0',
        cursor    : 'pointer',
        background: 'url(/wp-content/themes/child/includes/alcocalc/data/picker2.png) center top no-repeat',
        width     : '12px',
        height    : '20px',
        marginLeft: '-6px'     
        
    },
    CSS_MIN = {
        position : 'absolute',
        left     : '0px',
        marginTop: '-1.4em',
        color    : '#BBB',
        textAlign: 'left',
        fontSize : '0.9em'
    },
    CSS_MAX = {
        position : 'absolute',
        right    : '0px',
        marginTop: '-1.4em',
        color    : '#BBB',
        textAlign: 'right',
        fontSize : '0.9em'
    },
    CSS_VALUE = {
        position : 'absolute',
        width    : '100%',
        textAlign: 'center',
        marginTop: '-1.4em',
        fontSize : '1.1em'
    }

$.fn.picker = function (options)
{   
    var self = this; 
    var defaults = {
        width : '200px',
        max : 100,
        min : 0,
        decimalLimit: 0,
        unit: '',
        localeUnitKey: '',
        zeroUnit: '',
        onChange: function (value) {}
    }
    options = this.options = $.extend (defaults, options);
    
    this.setOptions = function (newOptions) {
        options = $.extend (options, newOptions);
        init();
    }
        
    function init ()
    {
        $min.html(options.min + '&thinsp;' + options.unit);
        $max.html(options.max + '&thinsp;' + options.unit);
    }
    
    this.css(CSS_PICKER_AREA).css('width', options.width);
    
    /*
	 * Value, Min & Max
	 */
	var $min = $('<span class="min"></span>').css(CSS_MIN),
	    $max = $('<span class="max"></span>').css(CSS_MAX);

	this.$value = $('<div></div>').css(CSS_VALUE);	
	this.append($min).append($max).append(this.$value);        
	
	/*
	 * For events
	 */
	this.$input = $('<input name="strong"/>');
	this.append( this.$input.hide() );
    
    /*
     * Dragging
     */
    var $picker = $('<div></div>').css(CSS_PICKER).addClass('picker');    
    this.append($picker); 
	
	$picker.bind('drag', {parent: this},        
		function (e) {    
		    setPickerOffset(
		        $(this), e.data.parent, e.offsetX
		    );
		}
	);
	
	function setPickerOffset ($picker, $parent, offset)
	{   
	    var pickerWidth = $picker.width() / 2,
            dragWidth = $parent.width(),
            dragAreaOffset = $parent.offset().left,
            interval = Math.abs($parent.options.max) - Math.abs($parent.options.min);
    
        offset -= dragAreaOffset;
        offset += pickerWidth;                
    
    	var x = offset > dragWidth || offset < 0  
    			? $picker.css('left')
    			: offset + 'px'; 
    					   				
    	$picker.css('left', x);
    	
    	if (offset > dragWidth) offset = dragWidth;
    	if (offset < 0) offset = 0;
	
	    var value = $parent.options.min + offset / dragWidth * interval;
    	if ($parent.options.decimalLimit)
    	    value = value <= $parent.options.decimalLimit 
    	        ? Math.round(value*10) / 10 
    	        : Math.round(value);
    	else
    	    value = Math.round(value);
	    
	    var unit = $parent.options.localeUnitKey 
	        ? UnitCase($parent.options.localeUnitKey, value)
	        : $parent.options.unit;
        
        var strValue = value == 0 && $parent.options.zeroUnit 
            ? $parent.options.zeroUnit
            : value.number() + '&thinsp;' + unit;
	        
    	$parent.$value.html(strValue);
    	$parent.options.onChange(value);    	    	
    	
    	$picker.attr('value', value);
    	$parent.$input.trigger('keyup');
	}	
	
	this.setValue = function (value)
	{
	    var interval = Math.abs(self.options.max) - Math.abs(self.options.min);
	    setPickerOffset(
	        $picker, self, 
	        (value - self.options.min) / interval * self.width() + self.offset().left - $picker.width() / 2
	    ); 
	}	
	
	init();
	return this;
}
