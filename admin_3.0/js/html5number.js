/*

html5number - a JS implementation of <input type=number> for Firefox 16 and up

BASED ON :

html5slider - a JS implementation of <input type=range> for Firefox 16 and up
https://github.com/fryn/html5slider


*/
var c = console.log;
(function() {

// test for native support
var test = document.createElement('input');
try {
  test.type = 'number';
  if (test.type == 'number')
    return;
} catch (e) {
  return;
}

// test for required property support
test.style.background = 'linear-gradient(red, red)';
if (!test.style.backgroundImage || !('MozAppearance' in test.style) || !document.mozSetImageElement || !this.MutationObserver)
	return;

var scale;
var thumb = {  radius:  6,  width:  12,  height:  20};
var styles = {
  'min-width': thumb.width + 'px',
  'min-height': thumb.height + 'px',
  'max-height': thumb.height + 'px',
};
var options = {
  attributes: true,
  attributeFilter: ['min', 'max', 'step', 'value']
};
var forEach = Array.prototype.forEach;
var onChange = document.createEvent('HTMLEvents');
onChange.initEvent('change', true, false);

if (document.readyState == 'loading')
  document.addEventListener('DOMContentLoaded', initialize, true);
else
  initialize();

function initialize() {
  // create initial sliders
  forEach.call(document.querySelectorAll('input[type=number]'), transform);
  // create sliders on-the-fly
  new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.addedNodes)
        forEach.call(mutation.addedNodes, function(node) {
          check(node);
          if (node.childElementCount)
            forEach.call(node.querySelectorAll('input'), check);
        });
    });
  }).observe(document, { childList: true, subtree: true });
}

function check(input) {
  if (input.localName == 'input' && input.type != 'number' &&
      input.getAttribute('type') == 'number')
    transform(input);
}

function transform(slider) { //start transform

  var isValueSet, areAttrsSet, isChanged, isClick, prevValue, rawValue, prevX;
  var min, max, step, number, value = slider.value;

  // lazily create shared slider affordance
  if (!scale) {
    scale = document.body.appendChild(document.createElement('hr'));
    style(scale, {
      '-moz-appearance': 'scalethumb-vertical',
      display: 'block',
      visibility: 'visible',
      opacity: 1,
      position: 'fixed',
      top: '-999999px'
    });
    document.mozSetImageElement('__scalethumb_T__', scale);
    document.mozSetImageElement('__scalethumb_Y__', scale);
  }

  // reimplement value and type properties
  var getValue = function() { return '' + value; };
  var setValue = function setValue(val) {
    value = '' + val;
    isValueSet = true;
    draw();
    delete slider.value;
    slider.value = value;
    slider.__defineGetter__('value', getValue);
    slider.__defineSetter__('value', setValue);
  };
  slider.__defineGetter__('value', getValue);
  slider.__defineSetter__('value', setValue);
  slider.__defineGetter__('type', function() { return 'number'; });

  // sync properties with attributes
  ['min', 'max', 'step'].forEach(function(prop) {
    if (slider.hasAttribute(prop))
      areAttrsSet = true;
    slider.__defineGetter__(prop, function() {
      return this.hasAttribute(prop) ? this.getAttribute(prop) : '';
    });
    slider.__defineSetter__(prop, function(val) {
      val === null ? this.removeAttribute(prop) : this.setAttribute(prop, val);
    });
  });

  // initialize slider
  slider.readOnly = false;
  style(slider, styles);
  update();

  new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName != 'value') {
        update();
        areAttrsSet = true;
      }
      // note that value attribute only sets initial value
      else if (!isValueSet) {
        value = slider.getAttribute('value');
        draw();
      }
    });
  }).observe(slider, options);

  slider.addEventListener('keydown', onKeyDown, true);

  function onKeyDown(e) {
    if (e.keyCode > 36 && e.keyCode < 41) { // 37-40: left, up, right, down
      isChanged = true;
      this.value = value + (e.keyCode == 38 || e.keyCode == 39 ? step : -step);
    }
  }

  // determines whether value is valid number in attribute form
  function isAttrNum(value) {
    return !isNaN(value) && +value == parseFloat(value);
  }

  // validates min, max, and step attributes and redraws
  function update() {
    min = isAttrNum(slider.min) ? +slider.min : -100000;
    max = isAttrNum(slider.max) ? +slider.max : 100000;
    if (max < min)
      max = min > 100 ? min : 100;
    step = isAttrNum(slider.step) && slider.step > 0 ? +slider.step : 1;
    number = max - min;
    draw(true);
  }

  // recalculates value property
  function calc() {
    if (!isValueSet && !areAttrsSet)
      value = slider.getAttribute('value');
    if (!isAttrNum(value))
      value = (min + max) / 2;
    // snap to step intervals (WebKit sometimes does not - bug?)
    value = Math.round((value - min) / step) * step + min;
    if (value < min)
      value = min;
    else if (value > max)
      value = min + ~~(number / step) * step;
  }

  // renders slider using CSS background ;)
  function draw(attrsModified) {
    calc();
    if (isChanged && value != prevValue)
      slider.dispatchEvent(onChange);
    isChanged = false;
    if (!attrsModified && value == prevValue)
      return;
    prevValue = value;
    var bg = '-moz-element(#__scalethumb_T__) 100% 0 no-repeat, -moz-element(#__scalethumb_Y__) 100% 100% no-repeat ';
	//for this moment inutile to show this bg's
    //style(slider, { background: bg  });
  }

}//trnasform

function style(element, styles) {
  for (var prop in styles)
    element.style.setProperty(prop, styles[prop], 'important');
}

})();
/***/
var  extractNumDecimalDigits = function(input) {
          var num, raisedNum;
          if (input != null) {
            num = 0;
            raisedNum = input;
            while (raisedNum !== Math.round(raisedNum)) {
              num += 1;
              raisedNum = input * Math.pow(10, num);
            }
            return num;
          } else {
            return 0;
          }
};
var    matchStep = function(value, min, max, step) {
          var mod, raiseTo, raisedMod, raisedStep, raisedStepDown, raisedStepUp, raisedValue, stepDecimalDigits, stepDown, stepUp;
          stepDecimalDigits = extractNumDecimalDigits(step);
          if (step == null) {
            return value;
          } else if (stepDecimalDigits === 0) {
            mod = (value - (min || 0)) % step;
            if (mod === 0) {
              return value;
            } else {
              stepDown = value - mod;
              stepUp = stepDown + step;
              if ((stepUp > max) || ((value - stepDown) < (stepUp - value))) {
                return stepDown;
              } else {
                return stepUp;
              }
            }
          } else {
            raiseTo = Math.pow(10, stepDecimalDigits);
            raisedStep = step * raiseTo;
            raisedMod = (value - (min || 0)) * raiseTo % raisedStep;
            if (raisedMod === 0) {
              return value;
            } else {
              raisedValue = value * raiseTo;
              raisedStepDown = raisedValue - raisedMod;
              raisedStepUp = raisedStepDown + raisedStep;
              if (((raisedStepUp / raiseTo) > max) || ((raisedValue - raisedStepDown) < (raisedStepUp - raisedValue))) {
                return raisedStepDown / raiseTo;
              } else {
                return raisedStepUp / raiseTo;
              }
            }
          }
};

/*
var  newVal = clipValues(params['val'], params['min'], params['max']);
                newVal = matchStep(newVal, params['min'], params['max'], params['step'], params['stepDecimal']);
                $(this).val(newVal);
*/				
var clipValues = function(value, min, max) {
          if ((max != null) && value > max) {
            return max;
          } else if ((min != null) && value < min) {
            return min;
          } else {
            return value;
          }
        };