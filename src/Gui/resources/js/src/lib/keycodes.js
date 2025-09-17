import $ from 'jquery'

export const Keycodes = {
    BACKSPACE: 8,
    TAB: 9,
    ENTER: 13,
    SHIFT: 16,
    CTRL: 17,
    ALT: 18,
    ESCAPE: 27,
    PAGE_UP: 33,
    PAGE_DOWN: 34,
    END: 35,
    HOME: 36,
    ARROW_LEFT: 37,
    ARROW_UP: 38,
    ARROW_RIGHT: 39,
    ARROW_DOWN: 40,
    DELETE: 46,
    DECIMAL_POINT: 110,
    COMMA: 188,
    PERIOD: 190
}

const modifierKeys = {
    shift: false,
    ctrl: false,
    alt: false
};

export const isShiftPressed = () => modifierKeys.shift
export const isCtrlPressed = () => modifierKeys.ctrl
export const isAltPressed = () => modifierKeys.alt

$(document).on('keydown', e => {
    switch(e.keyCode) {
        case Keycodes.SHIFT:
            modifierKeys.shift = true
            break
        case Keycodes.CTRL:
            modifierKeys.ctrl = true
            break
        case Keycodes.ALT:
            modifierKeys.alt = true
            break
    }
}).on('keyup', e => {
    switch(e.keyCode) {
        case Keycodes.SHIFT:
            modifierKeys.shift = false
            break
        case Keycodes.CTRL:
            modifierKeys.ctrl = false
            break
        case Keycodes.ALT:
            modifierKeys.alt = false
            break
    }
})

$(window).on('blur', () => {
    modifierKeys.shift = false
    modifierKeys.ctrl = false
    modifierKeys.alt = false
})

$(window).on('focus', () => {
    modifierKeys.shift = false
    modifierKeys.ctrl = false
    modifierKeys.alt = false
})

export const getKeyCodeFromChar = (char) => {
    return char.codePointAt(0);
}