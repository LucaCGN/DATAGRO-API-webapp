**Descrição do Projeto:**

Este projeto envolve o desenvolvimento de uma aplicação web de página única para consulta à base de dados e output da API pela equipe do comercial. O projeto será construído usando Laravel e PHP, com SQLite como solução interna de banco de dados integrada ao framework Laravel. O número máximo de usuários simultâneos será de 10.

**Detalhamento das Atividades:**

1. **Requisição à API com as primitivas Cad e Dados:** A aplicação fará requisições à API Datagro para buscar dados e informações sobre produtos. Isso será feito através de funções específicas que buscam e processam esses dados.

2. **Caching dos resultados internamente na aplicação para consulta:** Para otimizar o desempenho da aplicação, os resultados das requisições à API serão armazenados em cache. Isso reduzirá a necessidade de requisições repetidas à API, melhorando a eficiência da aplicação.

3. **Uso por parte da equipe comercial para obter um sample dos dados providos pela API:** A aplicação será usada pela equipe comercial para acessar e analisar os dados fornecidos pela API. Isso permitirá que a equipe comercial faça uma análise detalhada dos dados e obtenha insights valiosos.

**Especificação do Servidor:**

CENÁRIO SERVIDOR FIXO: 2 núcleos de CPU, 4GB de RAM e 20GB de armazenamento.

CENÁRIO INFRAESTRUTURA NUVEM ESCALEÁVEL: O consumo de recursos será calculado com base no número de usuários e na complexidade das operações realizadas. Por exemplo, se cada usuário realiza uma média de 100 operações por dia, o servidor precisará ser capaz de lidar com 1000 operações por dia.

**Armazenamento Necessário:**

1GB para a base de dados, o código da aplicação e os logs. Isso permitirá que a aplicação funcione de maneira eficiente e mantenha um registro adequado de suas atividades.

**Exemplo de Código:**

```php
def fetch_data_series(product_code, start_date, end_date):
  url = f"https://precos.api.datagro.com/dados/?a={product_code}&i={start_date}&f={end_date}&x=c"
  return make_request_with_retries(url)

def fetch_product_data(product_code):
  try:
      url = "https://precos.api.datagro.com/cad/"
      params = {'a': product_code, 'x': 'j'}	
```
