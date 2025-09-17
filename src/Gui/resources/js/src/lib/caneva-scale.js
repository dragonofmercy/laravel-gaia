export class CanevaScale {
    constructor(canva){
        this.canva = canva
    }

    scale(factor = 0.8) {
        this._validateScale(factor);

        const dimensions = this._calculateDimensions(factor);
        const buffers = this._initializeBuffers(dimensions);

        this._processPixels(dimensions, buffers);

        return this._createResultCanvas(dimensions, buffers.target);
    }

    _validateScale(factor) {
        if (factor <= 0 || factor >= 1) {
            throw new Error('Le facteur d\'échelle doit être un nombre positif < 1');
        }
    }

    _calculateDimensions(factor) {
        const sw = this.canva.width;
        const sh = this.canva.height;
        return {
            sw, sh,
            tw: Math.ceil(sw * factor),
            th: Math.ceil(sh * factor),
            scale: factor,
            sqScale: factor * factor
        };
    }

    _initializeBuffers({ sw, sh, tw, th }) {
        return {
            source: this.canva.getContext('2d').getImageData(0, 0, sw, sh).data,
            target: new Float32Array(4 * tw * th)
        };
    }

    _processPixels(dimensions, buffers) {
        const { sw, sh, tw, scale, sqScale } = dimensions;
        const { source: sBuffer, target: tBuffer } = buffers;

        let sIndex = 0;

        for (let sy = 0; sy < sh; sy++) {
            const ty = sy * scale;
            const tY = Math.floor(ty);
            const yIndex = 4 * tY * tw;
            const crossY = tY !== Math.floor(ty + scale);

            let wy, nwy;
            if (crossY) {
                wy = tY + 1 - ty;
                nwy = ty + scale - tY - 1;
            }

            for (let sx = 0; sx < sw; sx++, sIndex += 4) {
                const tx = sx * scale;
                const tX = Math.floor(tx);
                const tIndex = yIndex + tX * 4;
                const crossX = tX !== Math.floor(tx + scale);

                let wx, nwx;
                if (crossX) {
                    wx = tX + 1 - tx;
                    nwx = tx + scale - tX - 1;
                }

                const rgba = [
                    sBuffer[sIndex],
                    sBuffer[sIndex + 1],
                    sBuffer[sIndex + 2],
                    sBuffer[sIndex + 3]
                ];

                this._distributePixel(tBuffer, tIndex, rgba, {
                    crossX, crossY, wx, nwx, wy, nwy, scale, sqScale, tw
                });
            }
        }
    }

    _distributePixel(tBuffer, tIndex, [r, g, b, a], options) {
        const { crossX, crossY, wx, nwx, wy, nwy, scale, sqScale, tw } = options;

        if (!crossX && !crossY) {
            this._addWeightedPixel(tBuffer, tIndex, [r, g, b, a], sqScale);
        } else if (crossX && !crossY) {
            const w = wx * scale;
            const nw = nwx * scale;
            this._addWeightedPixel(tBuffer, tIndex, [r, g, b, a], w);
            this._addWeightedPixel(tBuffer, tIndex + 4, [r, g, b, a], nw);
        } else if (crossY && !crossX) {
            const w = wy * scale;
            const nw = nwy * scale;
            this._addWeightedPixel(tBuffer, tIndex, [r, g, b, a], w);
            this._addWeightedPixel(tBuffer, tIndex + 4 * tw, [r, g, b, a], nw);
        } else {
            // Quatre points impliqués
            const weights = [wx * wy, nwx * wy, wx * nwy, nwx * nwy];
            const indices = [tIndex, tIndex + 4, tIndex + 4 * tw, tIndex + 4 * tw + 4];

            indices.forEach((idx, i) => {
                this._addWeightedPixel(tBuffer, idx, [r, g, b, a], weights[i]);
            });
        }
    }

    _addWeightedPixel(buffer, index, [r, g, b, a], weight) {
        buffer[index] += r * weight;
        buffer[index + 1] += g * weight;
        buffer[index + 2] += b * weight;
        buffer[index + 3] += a * weight;
    }

    _createResultCanvas({ tw, th }, tBuffer) {
        const canvas = document.createElement('canvas');
        canvas.width = tw;
        canvas.height = th;

        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, tw, th);
        const targetBuffer = imageData.data;

        // Conversion plus efficace
        for (let i = 0; i < tBuffer.length; i++) {
            targetBuffer[i] = Math.min(255, Math.ceil(tBuffer[i]));
        }

        ctx.putImageData(imageData, 0, 0);
        return canvas;
    }
}

export default CanevaScale