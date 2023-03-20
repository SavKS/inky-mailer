import { Inky } from 'inky';
import express from 'express';
import minimist from 'minimist';
import { load } from 'cheerio';
import log from 'fancy-log';
import nodeFs from 'node:fs';
import inlineCss from 'inline-css';
import { minify } from 'html-minifier-terser';
import asyncHandler from 'express-async-handler';

const argv = minimist(
    process.argv.slice(2)
);

const app = express();

app.use(
    express.json({ limit: '50mb' })
);

app.post('/render', asyncHandler(async (req, res) => {
    const inky = new Inky();

    const inkyHtml = req.body.html;
    const url = req.body.url;
    const options = req.body.options ?? {};

    const startTime = performance.now();

    let html = inky.releaseTheKraken(
        load(inkyHtml)
    );

    const usedOptions = [];

    if (options.inlineCss) {
        html = await inlineCss(html, {
            url: url || 'http://localhhost',
            removeStyleTags: true,
            preserveMediaQueries: true,
            removeLinkTags: false
        });

        usedOptions.push('inline css');
    }

    if (options.minify) {
        html = await minify(html, {
            collapseWhitespace: true,
            minifyCSS: true
        });

        usedOptions.push('minify');
    }

    const endTime = performance.now()

    console.log(`Rendering time${ usedOptions.length ? `[${ usedOptions.join(', ') }]` : '' }: ${ endTime - startTime } milliseconds`)

    res.json({ html })
}));

const port = argv.port;
const host = argv.host;
const unixSocket = argv.path;

if (!host && !port && !unixSocket) {
    throw new Error('Unix-socket path or port with host must be specified');
}

if (port) {
    app.listen(port, host, () => {
        log(`Listening on: http://${ host }:${ port }`)
    });
} else {
    if (!unixSocket.startsWith('/')) {
        throw new Error('Unix socket file path must be absolute');
    }

    if (nodeFs.existsSync(unixSocket)) {
        nodeFs.unlinkSync(unixSocket);
    }

    app.listen(unixSocket, () => {
        log(`Listening on unix socket: ${ unixSocket }`)
    });

    const removeSocket = () => {
        process.off('beforeExit', removeSocket);
        process.off('SIGINT', removeSocket);
        process.off('SIGTERM', removeSocket);

        if (nodeFs.existsSync(unixSocket)) {
            nodeFs.unlinkSync(unixSocket);
        }

        process.exit();
    };

    process.on('beforeExit', removeSocket);
    process.on('SIGINT', removeSocket);
    process.on('SIGTERM', removeSocket);
}
