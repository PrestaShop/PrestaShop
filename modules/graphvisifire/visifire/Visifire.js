/*
    Copyright (C) 2008 Webyog Softworks Private Limited

    This file is a part of Visifire Charts.
 
    Visifire is a free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
      
    You should have received a copy of the GNU General Public License
    along with Visifire Charts.  If not, see <http://www.gnu.org/licenses/>.
  
    If GPL is not suitable for your products or company, Webyog provides Visifire 
    under a flexible commercial license designed to meet your specific usage and 
    distribution requirements. If you have already obtained a commercial license 
    from Webyog, you can use this file under those license terms.
*/

if(!window.Visifire)
{
    // Visifire class
    window.Visifire = function(pXapPath, pId, pWidth, pHeight, pBackground)
    {
		this.id = null;
        this.logLevel = 1;                      //  Determines whether to log or not.
        this.xapPath = "Visifire.xap";          //  Default is taken as Visifire.xap in the same directory.
        this.targetElement = null;              
        this.dataXml = null;                    
        this.dataUri = null;
        this.listeners = null;                  
        this.elements = new Array("Chart", "DataPoint", "Title", "AxisX", "AxisY", "Legend");
        this.events = new Array("MouseLeftButtonDown", "MouseLeftButtonUp", "MouseMove", "MouseEnter", "MouseLeave");
        this.windowless = false;        
        this.width = null;
        this.height = null;
        this.background = null;

        // pId not present
		if(Number(pId))
		{
			if(pHeight)
                this.background = pHeight;
            
            pHeight = pWidth;
			pWidth = pId; 
		}
		else // pId present
		{
		    this.id = pId;
		    
		    if(pBackground)
                this.background = pBackground;
		}
				
        if(pXapPath)
            this.xapPath = pXapPath;
            
        if(pWidth)
            this.width = pWidth;
            
        if(pHeight)
            this.height = pHeight;
                                 
        this._uThisObject = this;               
            
        this.index = ++Visifire._slCount;
    }
    
    window.Visifire._slCount = 0;
    
    Visifire.prototype.setWindowlessState = function(pWindowless)
    {
        if(pWindowless != null)
        {
            this.windowless = Boolean(pWindowless);
        }
    }
    
    Visifire.prototype._getSlControl = function ()
    {
        var _uThisObject = this;
        if(_uThisObject.id != null)
        {
            var slControl = document.getElementById(_uThisObject.id);
            return slControl;
        }
        
        return null;        
    }
    
    Visifire.prototype.isLoaded = function()
    {
        var slControl = this._getSlControl();
        try
        {
            if(slControl.Content.wrapper != null)
                return true;
        }
        catch(ex)
        {
            return false;
        }
    }
    Visifire.prototype.isDataLoaded = function()
    {
        var slControl = this._getSlControl();
        
        return slControl.Content.wrapper.IsDataLoaded;
    }
    Visifire.prototype.setSize = function(pWidth,pHeight)
    {
        var slControl = this._getSlControl();
        if(slControl != null)
        {
            slControl.width = pWidth;
            slControl.height = pHeight;
            slControl.Content.wrapper.Resize(pWidth,pHeight);
        }
        else
        {
            this.width = pWidth;
            this.height = pHeight;
        }
    }
    
    Visifire.prototype.setDataXml = function(pDataXml)
    {
        var slControl = this._getSlControl();
        
        if(slControl != null && this.dataXml != null)
        {
            slControl.Content.wrapper.AddDataXML(pDataXml);
        }
        
        this.dataXml = pDataXml;
    }
    
   
    Visifire.prototype.setDataUri = function(pDataUri)
    {
        var slControl = this._getSlControl();
        
        if(slControl != null && this.dataUri != null)
        {
            slControl.Content.wrapper.AddDataUri(pDataUri);
        }
        
        this.dataUri = pDataUri;
    }
    
    Visifire.prototype.setLogLevel = function(level)
    {
        if(level != null)
        {
            this.logLevel = level;
        }
    }        
    
    Visifire.prototype._isString = function() 
    {
        if (typeof arguments[0] == 'string') return true; 
        
        if (typeof arguments[0] == 'object') 
        {  
            var criterion = arguments[0].constructor.toString().match(/string/i); 
            return (criterion != null);  
        }
        
        return false;
    }
    
    Visifire.prototype._validateChartElement = function(pElement)
    {
        if(this.logLevel != 0)
        {           
            for(var i = 0; i < this.elements.length; i++)
                if(this.elements[i] == pElement)
                    return;
             
            alert('Error occurred while attaching event.\nUnknown element "' + pElement + '".');
        }
    }
    
    Visifire.prototype._validateEvent = function(pEvent)
    {
        if(this.logLevel != 0)
        {
            for(var i = 0; i < this.events.length; i++)
                if(this.events[i] == pEvent)
                    return;
            
            alert('Error occurred while attaching event.\nUnsupported event type "' + pEvent + '".');
        }
    }
    
    Visifire.prototype.attachEvent = function(pElement, pEvent, pCallBack)
    {
        var _uThisObject = this;
        
        _uThisObject._validateChartElement(pElement);
        _uThisObject._validateEvent(pEvent);
        
        if(pEvent && pElement &&  pCallBack)
        {   
            if(_uThisObject.listeners == null)
                _uThisObject.listeners =  {};
                
            if(_uThisObject.listeners[pEvent] == null)
                _uThisObject.listeners[pEvent] = new Array(); 
                
            if(!window["dispatchEvent" + _uThisObject.index])
                window["dispatchEvent" + _uThisObject.index] = function(args)
                {   
                    if(_uThisObject.listeners[args.Event] != null)
                    {   
                        var listener = _uThisObject.listeners[args.Event]; 
                        if(listener.length != 0)               
                        {
                            for (var i = 0; i < listener.length; i++)
                            {   
	                            if ((listener[i].event == args.Event) && (listener[i].element == args.Element))
	                            {
									args.ControlId = _uThisObject.id;
															
                                    if(_uThisObject._isString(listener[i].fire))
                                        eval(listener[i].fire + "(args)"); 
                                    else
                                        listener[i].fire(args); 
	                            }
                            }
                        }
                    }
                };
                
            _uThisObject.listeners[pEvent].push({element: pElement, event: pEvent, fire: pCallBack});
        }
    }
    
    Visifire.prototype._render = function(pTargetElement)
    {
        var _uThisObject = this;
        var width;
        var height;
        
        _uThisObject.targerElement = (typeof(pTargetElement) == "string")?document.getElementById(pTargetElement):pTargetElement;
        
        if(_uThisObject.width != null)
            width = _uThisObject.width;
        else if(_uThisObject.targerElement.offsetWidth != 0)
            width = _uThisObject.targerElement.offsetWidth;
        else
            width = 500;
        
        if(_uThisObject.height != null)
            height = _uThisObject.height;
        else if(_uThisObject.targerElement.offsetHeight != 0)
            height = _uThisObject.targerElement.offsetHeight;
        else
            height = 300;
				
        if(!_uThisObject.id)
		{
		    _uThisObject.id = 'VisifireControl' + _uThisObject.index;
		}
        
		var html = '<object id="' + _uThisObject.id + '" data="data:application/x-silverlight-2," type="application/x-silverlight-2" width="' + width + '" height="' + height + '">';
        
        html    +=  '<param name="source" value="' + _uThisObject.xapPath +'"/>'
		        +	'<param name="onLoad" value="slLoaded' + _uThisObject.index +'"/>'
	            +   '<param name="onResize" value="slResized' + _uThisObject.index +'"/>';
		html += '<param name="initParams" value="';
		
		html += "logLevel=" + _uThisObject.logLevel + ",";
		
        if(_uThisObject.dataXml != null)
        {   
            window["getDataXml"+_uThisObject.index] = function(sender, args)
            {   
                var _uThisObj = _uThisObject;
                return _uThisObj.dataXml;
            };
                                             
            html +=	'dataXml=getDataXml'+ _uThisObject.index  +',';
        }
        else if(_uThisObject.dataUri != null)
        {   
            html +=	'dataUri='+ _uThisObject.dataUri  +',';
        }
        
        if(_uThisObject.listeners != null)
        {
            html += 'EventDispatcher=dispatchEvent' + _uThisObject.index + ',';
            
            html += 'jsEvents=';
            
            var events = _uThisObject.events;

            for(var i=0; i< events.length; i++)
            {   
                var listener = _uThisObject.listeners[events[i]]; 
                
                if(listener != null)
                {
                    for (var j = 0; j < listener.length; j++)
                    {   
                        html += listener[j].element + ' ' + listener[j].event + ';';
                    }
                }
            }
            
            html += ','
        }
        
         
        if(_uThisObject.background == null)
            _uThisObject.background = "White";
        
        html    += 'width=' + width + ',' + 'height=' + height + '';
        html    += "\"/>";
        html    += '<param name="enableHtmlAccess" value="true" />'
		        +  '<param name="background" value="' + _uThisObject.background + '" />'
		        +  '<param name="windowless" value="' + this.windowless + '" />'
		        + '<a href="http://go.microsoft.com/fwlink/?LinkID=124807" style="text-decoration: none;">'
		        + '<img src="http://go.microsoft.com/fwlink/?LinkId=108181" alt="Get Microsoft Silverlight" style="border-style: none"/>'
		        +  '<br/>You need Microsoft Silverlight to view Visifire Charts.'
		        +  '<br/> You can install it by clicking on this link.'
		        +  '<br/>Please restart the browser after installation.'
		        +  '</a>'
		        +  '</object>';
		
		this.targerElement.innerHTML = html;
    }
    
    Visifire.prototype._reRender = function(pSlControl)
    {
        pSlControl.Content.wrapper.ReRenderChart();
    }
    
    Visifire.prototype.render = function(pTargetElement)
    {
        var slControl = this._getSlControl();
        
        if(slControl == null)
        {   
            this._render(pTargetElement);       
		}
		else
		{
		    this._reRender(slControl);
		}
    }
}