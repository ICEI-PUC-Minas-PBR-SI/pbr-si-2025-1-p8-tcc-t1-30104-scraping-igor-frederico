import puppeteer from 'puppeteer';

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

(async () => {
    const patente_pesquisada = process.argv[2];

    if (!patente_pesquisada) {
        console.error('Você precisa informar o nome da patente. Ex: node scrapper_att.js "aspirin"');
        process.exit(1);
    }

    const browser = await puppeteer.launch({
        headless: true,
        executablePath: '/home/ign/.cache/puppeteer/chrome/linux-135.0.7049.42/chrome-linux64/chrome',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setRequestInterception(true);
    page.on('request', (request) => request.continue());

    await page.goto('https://ppubs.uspto.gov/pubwebapp/static/pages/ppubsbasic.html', {
        waitUntil: 'domcontentloaded',
        timeout: 60000
    });

    await page.waitForSelector('input#searchText1', { timeout: 60000 });
    await page.waitForSelector('button#basicSearchBtn', { timeout: 60000 });

    const allResults = [];

    // função auxiliar para coletar dados da página atual
    const getDocsFromCurrentPage = async () => {
        const response = await page.waitForResponse(response =>
            response.url().includes('searches/generic') && response.status() === 200,
            { timeout: 30000 }
        );
        const json = await response.json();
        return json?.docs || [];
    };

    const goToNextPageAndGetDocs = async () => {
        const nextBtn = await page.$('#paginationNextItem:not(.disabled)');
        if (nextBtn) {
            const [response] = await Promise.all([
                page.waitForResponse(res =>
                    res.url().includes('searches/generic') && res.status() === 200,
                    { timeout: 60000 }
                ),
                nextBtn.click(),
            ]);
            await delay(2000); // evitar sobrecarga
            const json = await response.json();
            return json?.docs || [];
        }
        return [];
    };

    for (let day = 1; day <= 31; day++) {
        const dayStr = day.toString().padStart(2, '0'); 
        const dateFormatted = `202501${dayStr}`;
        await page.evaluate(() => {
            document.querySelector('input#searchText1').value = '';
            document.querySelector('input#searchText2').value = '';
        });

        await page.select('#searchField2', 'PD');
        await page.type('input#searchText2', dateFormatted);
        await page.type('input#searchText1', patente_pesquisada);
        await page.click('button#basicSearchBtn');

        try {
            let docs = await getDocsFromCurrentPage();
            if (docs.length) {
                allResults.push(...docs);
                while (true) {
                    const nextDocs = await goToNextPageAndGetDocs();
                    if (!nextDocs.length) break;
                    allResults.push(...nextDocs);
                }
            }
        } catch (error) {
            console.error(`Erro buscando dados para o dia ${dateFormatted}:`, error.message);
        }

        await delay(1000);
    }


    console.log(JSON.stringify(allResults, null, 2));

    await browser.close();
})();
