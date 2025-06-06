import puppeteer from 'puppeteer';

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

(async () => {
    const patentNumber = process.argv[2];

    if (!patentNumber) {
        console.error('Você precisa informar o número da patente. Ex: node scrapper_claims.js "20250003983"');
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

    // Acessa a página de pesquisa
    await page.goto('https://ppubs.uspto.gov/pubwebapp/static/pages/ppubsbasic.html', {
        waitUntil: 'domcontentloaded',
        timeout: 60000
    });

    // Aguarda campos e pesquisa a patente
    await page.waitForSelector('input#searchText1', { timeout: 60000 });
    await page.waitForSelector('button#basicSearchBtn', { timeout: 60000 });

    await page.type('input#searchText1', patentNumber);
    await page.click('button#basicSearchBtn');

    // Espera os dados da sessão e da patente
    const response_session = await page.waitForResponse(
        response => response.url().includes('users/me/session') && response.status() === 200,
        { timeout: 60000 }
    );

    const response = await page.waitForResponse(
        response => response.url().includes('searches/generic') && response.status() === 200,
        { timeout: 60000 }
    );

    const headers = await response_session.headers();
    const token = headers['x-access-token'];
    const json_response = await response.json();

    const doc = json_response.docs.find(d => d.patentNumber === patentNumber);
    if (!doc) {
        console.error('Patente não encontrada.');
        await browser.close();
        return;
    }

    // Abre a página específica da patente para extrair as claims
    const info_page_patent = await browser.newPage();
    const url = `https://ppubs.uspto.gov/api/patents/html/${patentNumber}?source=${doc.type}&requestToken=${token}`;

    try {
        await info_page_patent.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 10000
        });

        await info_page_patent.waitForSelector('section.bottom-border.padding', { timeout: 30000 });

        const claims = await info_page_patent.evaluate(() => {
            const claims_section = Array.from(document.querySelectorAll('section.bottom-border.padding'))
                .find(section => {
                    const header = section.querySelector('h3');
                    return header && header.innerText.includes('Claims');
                });

            if (!claims_section) return [];

            const text = claims_section.innerText;
            const rawClaims = text.split(/\n(?=\d+\.\s)/).filter(claim => /^\d+\.\s/.test(claim));
            return rawClaims.map(claim => claim.trim());
        });

        console.log(JSON.stringify({ patentNumber, claims }, null, 2));
    } catch (error) {
        console.error("Erro ao extrair as claims:", error.message);
    } finally {
        await info_page_patent.close();
        await browser.close();
    }
})();
