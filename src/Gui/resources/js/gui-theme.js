gui.echarts_default_options = {
    grid: {
        left: '48px',
        right: '32px',
        bottom: '48px',
        top: '32px'
    },
    tooltip: {
        show: false,
        trigger: 'axis',
        padding: [7, 10],
        backgroundColor: gui.getCssVariable('--gui-body-bg'),
        borderColor: gui.getCssVariable('--gui-border-color'),
        borderWidth: 1,
        transitionDuration: 0,
        extraCssText: 'min-width: 140px;',
        textStyle: {
            color: gui.getCssVariable('--gui-body-color')
        },
    },
    legend: {
        show: false
    },
    dataZoom: [{
        type: 'inside',
    }, {
        show: false,
        type: 'slider',
        borderColor: gui.getCssVariable('--gui-border-color'),
        handleStyle: {
            color: gui.getCssVariable('--gui-card-bg'),
            borderColor: gui.getCssVariable('--gui-gray-400')
        },
        moveHandleStyle: {
            color: gui.getCssVariable('--gui-gray-400')
        },
        textStyle: {
            color: gui.getCssVariable('--gui-body-color')
        },
    }],
    textStyle: {
        fontFamily: gui.getCssVariable('--gui-font-sans-serif')
    },
    xAxis: {
        show: false,
        type: 'category',
        axisPointer: {
            lineStyle: {
                type: 'dashed',
                color: gui.getCssVariable('--gui-border-color'),
            }
        },
        axisLabel: {
            color: gui.getCssVariable('--gui-body-color'),
        },
        axisLine: {
            lineStyle: {
                color: gui.getCssVariable('--gui-border-color')
            }
        }
    },
    yAxis: {
        show: false,
        type: 'value',
        axisLine: {
            lineStyle: {
                color: gui.getCssVariable('--gui-border-color')
            }
        },
        axisLabel: {
            color: gui.getCssVariable('--gui-body-color')
        },
        splitLine: {
            lineStyle: {
                color: [
                    gui.getCssVariable('--gui-light-border-color')
                ]
            }
        }
    }
}