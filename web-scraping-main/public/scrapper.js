import puppeteer from 'puppeteer';

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

(async () => {
    const patente_pesquisada = process.argv[2];
    const maxPages = parseInt(process.argv[3], 10) || 1;
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

   
    const fetchDocumentsFromPage = async () => {
        const json_response = await response.json();
        const docs = json_response.docs.slice(0, 2); 
        return docs;
    };

    const goToNextPage = async () => {
        const nextBtn = await page.$('#paginationNextItem:not(.disabled)');
        if (nextBtn) {
            await nextBtn.click();
            await delay(3000); 
            return true;
        }
        return false;
    };


    const goToNextPageAndGetDocs = async () => {
        const nextBtn = await page.$('#paginationNextItem:not(.disabled)');
        if (nextBtn) {
            const [response] = await Promise.all([
                page.waitForResponse(response =>
                    response.url().includes('searches/generic') && response.status() === 200,
                    { timeout: 60000 }
                ),
                nextBtn.click(),
            ]);
    
            await delay(3000); 
            const json = await response.json();
            return json.docs.slice(0, 2);
        }
        return null;
    };

    const fetchAllDocuments = async () => {
        const results = [];
    
        const firstDocs = await fetchDocumentsFromPage(); 
        for (const [index, doc] of firstDocs.entries()) {
            if (index > 0) await delay(2000);
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
        }
    
        let currentPage = 0;
    
        while (currentPage <= maxPages) {
            const docs = await goToNextPageAndGetDocs();
            if (!docs || docs.length === 0) break;
    
            for (const [index, doc] of docs.entries()) {
                if (index > 0) await delay(2000);
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
            }

            currentPage++;
        }
    
        return results;
    };
    

    const resposta = await fetchAllDocuments();
    console.log(JSON.stringify(resposta, null, 2));

    await browser.close();
})();
