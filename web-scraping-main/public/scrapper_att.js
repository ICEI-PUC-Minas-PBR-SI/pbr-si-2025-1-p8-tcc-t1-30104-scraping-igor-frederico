import puppeteer from 'puppeteer';

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

(async () => {
    const patente_pesquisada = process.argv[2];

    if (!patente_pesquisada) {
        console.error('Você precisa informar o nome da patente. Ex: node scrapper_att.js "aspirin"');
        process.exit(1);
    }

    const browser = await puppeteer.launch({
        headless: false,
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

    await page.type('input#searchText1', patente_pesquisada);
    await page.click('button#basicSearchBtn');

    const response_session = await page.waitForResponse(response =>
        response.url().includes('users/me/session') && response.status() === 200,
        { timeout: 60000 }
    );

    const response = await page.waitForResponse(response =>
        response.url().includes('searches/generic') && response.status() === 200,
        { timeout: 60000 }
    );

    const headers = await response_session.headers();
    const token = headers['x-access-token'];
    const json_response = await response.json();

    // Reutiliza o mesmo browser, criando apenas novas páginas
    const fetchDocumentDetails = async (doc, token, mainBrowser) => {
        const info_page_patent = await mainBrowser.newPage();
        const patent_number = doc.patentNumber;
        const tipo = doc.type;
        const url_page_patent = `https://ppubs.uspto.gov/api/patents/html/${patent_number}?source=${tipo}&requestToken=${token}`;

        try {
            await info_page_patent.goto(url_page_patent, {
                waitUntil: 'domcontentloaded',
                timeout: 10000 // timeout mais generoso
            });

            await info_page_patent.waitForSelector('section.bottom-border.padding', { timeout: 30000 });
            await info_page_patent.waitForSelector('section.grid-container-12', { timeout: 30000 });

            const claims = await info_page_patent.evaluate(() => {
                const claims_section = Array.from(document.querySelectorAll('section.bottom-border.padding'))
                    .find(section => {
                        const header = section.querySelector('h3');
                        return header && header.innerText.includes('Claims');
                    });

                if (!claims_section) return [];
                const text = claims_section.innerText;
                const rawClaims = text.split(/\n(?=\d+\.\s)/).filter(claim => /^\d+\.\s/.test(claim));
                return rawClaims.map(claim => ({ claim: claim.trim() }));
            });

            const inventors = await info_page_patent.evaluate(() => {
                const inventorsLabel = Array.from(document.querySelectorAll('p'))
                    .find(p => p.textContent.trim() === 'Inventors:');

                if (!inventorsLabel) return [];

                const inventorsElement = inventorsLabel.nextElementSibling;
                if (!inventorsElement) return [];

                const rawText = inventorsElement.innerText;
                return rawText.split(/\),\s*/).map(item => item.replace(/[()]/g, '').trim());
            });

            await info_page_patent.close();
            return { claims, inventors };
        } catch (error) {
            console.error("Erro ao buscar os detalhes:", error.message);
            await info_page_patent.close();
            return { claims: [], inventors: [] };
        }
    };

    const fetchAllDocuments = async () => {
        const results = [];

        for (const [index, doc] of json_response.docs.slice(0, 40).entries()) {
            try {
                // Faz pausa para evitar 429
                if (index > 0) await delay(2000); // 2 segundos entre requisições

                const { claims, inventors } = await fetchDocumentDetails(doc, token, browser);

                results.push({
                    farmaco: patente_pesquisada,
                    documentId: doc.documentId,
                    datePublished: doc.datePublished,
                    title: doc.title,
                    patentNumber: doc.patentNumber,
                    inventors,
                    pageCount: doc.pageCount,
                    type: doc.type,
                    claims
                });
            } catch (error) {
                console.error(`Erro ao buscar detalhes do documento:`, error.message);
            }
        }
        console.log(results.length);
        return results;
    };

    const resposta = await fetchAllDocuments();
    console.log(JSON.stringify(resposta, null, 2));

    await browser.close();
})();
