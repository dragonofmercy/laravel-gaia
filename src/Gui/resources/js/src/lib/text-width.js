const textWidthCache = new Map();
const MAX_CACHE_SIZE = 1000;

let measurementElement = null;

function createMeasurementElement(){
    const container = document.createElement('div');
    container.style.cssText = 'position:absolute;overflow:hidden;width:0;height:0;visibility:hidden;';
    container.setAttribute('aria-hidden', 'true');

    const ruler = document.createElement('div');
    ruler.style.cssText = 'position:absolute;width:auto;padding:0;white-space:pre;';

    container.appendChild(ruler);
    document.body.appendChild(container);

    return { container, ruler };
}

function getMeasurementElement(){
    if(!measurementElement){
        measurementElement = createMeasurementElement();
    }
    return measurementElement;
}

export const textWidth = function(text, ref){
    if(!text || typeof text !== 'string'){
        return 0;
    }

    if(!ref || !ref.style){
        console.warn('textWidth: Reference not found');
        return 0;
    }

    const styleProps = [
        'fontFamily', 'fontWeight', 'fontStyle',
        'letterSpacing', 'textTransform', 'fontSize'
    ];

    const styleKey = styleProps.map(prop => ref.style[prop] || '').join('|');
    const cacheKey = `${text}::${styleKey}`;

    if(textWidthCache.has(cacheKey)){
        return textWidthCache.get(cacheKey);
    }

    try {
        const { container, ruler } = getMeasurementElement();
        const computedStyle = window.getComputedStyle(ref);
        ruler.style.fontFamily = computedStyle.fontFamily;
        ruler.style.fontWeight = computedStyle.fontWeight;
        ruler.style.fontStyle = computedStyle.fontStyle;
        ruler.style.fontSize = computedStyle.fontSize;
        ruler.style.letterSpacing = computedStyle.letterSpacing;
        ruler.style.textTransform = computedStyle.textTransform;

        ruler.textContent = text;
        const width = ruler.offsetWidth;

        if(textWidthCache.size >= MAX_CACHE_SIZE){
            const firstKey = textWidthCache.keys().next().value;
            textWidthCache.delete(firstKey);
        }

        textWidthCache.set(cacheKey, width);
        return width;
    } catch(error) {
        console.error("Error while calculation text width: ", error);
        return 0;
    }
};

export const cleanupTextWidth = function(){
    if(measurementElement){
        measurementElement.container.remove();
        measurementElement = null;
    }
    textWidthCache.clear();
};

if(typeof window !== 'undefined'){
    window.addEventListener('beforeunload', cleanupTextWidth);
}