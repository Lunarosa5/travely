--
-- PostgreSQL database dump
--

\restrict SQF8RY7eRut7xcESeRFjOe8uOIMlq60GTJbdciFC7WqB366gQXCFLVCOI3uGJaz

-- Dumped from database version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)
-- Dumped by pg_dump version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: fotos_viagem; Type: TABLE; Schema: public; Owner: travely
--

CREATE TABLE public.fotos_viagem (
    id integer NOT NULL,
    id_viagem integer NOT NULL,
    nome_arquivo character varying(255) NOT NULL
);


ALTER TABLE public.fotos_viagem OWNER TO travely;

--
-- Name: fotos_viagem_id_seq; Type: SEQUENCE; Schema: public; Owner: travely
--

CREATE SEQUENCE public.fotos_viagem_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fotos_viagem_id_seq OWNER TO travely;

--
-- Name: fotos_viagem_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: travely
--

ALTER SEQUENCE public.fotos_viagem_id_seq OWNED BY public.fotos_viagem.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: travely
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    senha character varying(255) NOT NULL
);


ALTER TABLE public.usuarios OWNER TO travely;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: travely
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO travely;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: travely
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: viagens; Type: TABLE; Schema: public; Owner: travely
--

CREATE TABLE public.viagens (
    id integer NOT NULL,
    id_usuario integer NOT NULL,
    titulo character varying(150) NOT NULL,
    destino character varying(150) NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL,
    relato text NOT NULL,
    avaliacao integer NOT NULL
);


ALTER TABLE public.viagens OWNER TO travely;

--
-- Name: viagens_id_seq; Type: SEQUENCE; Schema: public; Owner: travely
--

CREATE SEQUENCE public.viagens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.viagens_id_seq OWNER TO travely;

--
-- Name: viagens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: travely
--

ALTER SEQUENCE public.viagens_id_seq OWNED BY public.viagens.id;


--
-- Name: fotos_viagem id; Type: DEFAULT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.fotos_viagem ALTER COLUMN id SET DEFAULT nextval('public.fotos_viagem_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: viagens id; Type: DEFAULT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.viagens ALTER COLUMN id SET DEFAULT nextval('public.viagens_id_seq'::regclass);


--
-- Data for Name: fotos_viagem; Type: TABLE DATA; Schema: public; Owner: travely
--

COPY public.fotos_viagem (id, id_viagem, nome_arquivo) FROM stdin;
1	1	foto_6ad8a10922654_0.jpg
2	1	foto_6ad8a10925fa4_1.jpg
3	1	foto_6ad8a10929fd8_2.jpg
8	4	foto_6ad8a2e9e4e96_0.jpg
9	4	foto_6ad8a2e9e870d_1.jpg
10	4	foto_6ad8a2e9eba11_2.jpg
11	4	foto_6ad8a2e9ef3d0_3.jpg
12	4	foto_6ad8a2e9f2abb_4.jpg
13	4	foto_6ad8a2ea0220f_5.jpg
14	4	foto_6ad8a2ea056e6_6.jpg
15	4	foto_6ad8a2ea089c5_7.jpg
16	4	foto_6ad8a2ea0bd27_8.jpg
17	6	foto_6ad8a77364725_0.jpg
18	6	foto_6ad8a7736856d_1.jpg
19	6	foto_6ad8a7736bf1b_2.jpg
20	6	foto_6ad8a7736f89a_3.jpg
21	6	foto_6ad8a77372e9d_4.jpg
22	6	foto_6ad8a77376928_5.jpg
23	6	foto_6ad8a7737a84a_6.jpg
24	7	foto_6ad8a8100742e_0.jpg
25	7	foto_6ad8a8100b3be_1.jpg
26	7	foto_6ad8a8100f8e8_2.jpg
27	8	foto_6ad8a88b39c06_0.jpg
28	9	foto_6ad8a99cced59_0.jpg
29	9	foto_6ad8a99cd1df1_1.jpg
30	9	foto_6ad8a99cd5973_2.jpg
31	10	foto_6ad8ab366f4cb_0.jpg
32	10	foto_6ad8ab367793f_1.jpg
33	10	foto_6ad8ab367e48b_2.jpg
34	10	foto_6ad8ab368498e_3.jpg
35	10	foto_6ad8ab368d74c_4.jpg
36	10	foto_6ad8ab3698f06_5.jpg
43	12	foto_6ad8ac08485b6_0.jpg
44	12	foto_6ad8ac085163a_1.jpg
45	12	foto_6ad8ac08585e0_2.jpg
46	12	foto_6ad8ac085f1dd_3.jpg
47	13	foto_6ad8b4029f59f_0.jpg
48	13	foto_6ad8b402a4252_1.jpg
49	13	foto_6ad8b402a89e3_2.jpg
50	13	foto_6ad8b402ad9a5_3.jpg
51	13	foto_6ad8b402b2cc7_4.jpg
52	13	foto_6ad8b402b6f98_5.jpg
53	13	foto_6ad8b402baac4_6.jpg
54	13	foto_6ad8b402c0a9a_7.jpg
55	13	foto_6ad8b402c6748_8.jpg
56	13	foto_6ad8b402cb213_9.jpg
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: travely
--

COPY public.usuarios (id, nome, email, senha) FROM stdin;
1	Luna	luna@gmail.com	$2y$12$xcwUO9ipVfT8c3vyF5Ai8uCtAXaIiMTCQbdgVpqnZW0bHLpRhJcS6
\.


--
-- Data for Name: viagens; Type: TABLE DATA; Schema: public; Owner: travely
--

COPY public.viagens (id, id_usuario, titulo, destino, data_inicio, data_fim, relato, avaliacao) FROM stdin;
1	1	Viagem para Porto Seguro - Bahia	Porto Seguro - Bahia	2023-09-08	2023-09-15	A viagem para Porto Seguro foi excelente. O lugar é muito bonito e o roteiro rendeu ótimos passeios. De dia, deu para aproveitar bastante a estrutura das barracas na praia de Taperapuan e curtir o mar com tranquilidade. \r\nO passeio para o Centro Histórico valeu muito a pena para conhecer as igrejas antigas e o marco do descobrimento, além de ter uma vista linda lá de cima. Também deu tempo de ir até Trancoso para conhecer o Quadrado e a famosa igrejinha de fundo para o mar, que são pontos turísticos imperdíveis. À noite, a melhor opção foi caminhar pela Passarela do Descobrimento, que tem bastante variedade de restaurantes, música ao vivo e lojinhas de artesanato. \r\nNo geral, foi uma viagem super legal, muito bem aproveitada e com passeios que valem cada centavo!	5
4	1	Viagem para Itália	Itália	2024-10-15	2024-10-30	A viagem para a Itália foi excelente! O país é incrível e o roteiro rendeu passeios fantásticos por cidades cheias de história e paisagens marcantes.\r\nEm Roma, o destaque foi ver de perto o Coliseu e caminhar pelas ruínas do Fórum Romano, além de conhecer o Vaticano e a Fontana di Trevi, que são impressionantes. Em Florença, deu para aproveitar muito o clima cultural, as praças cheias de arte e a vista clássica da Ponte Vecchio. Para fechar, Veneza trouxe uma experiência totalmente diferente, sendo muito legal caminhar pelas ruelas cortadas pelos canais e ver a icônica Praça de São Marcos.\r\nA culinária também foi um ponto alto, com massas, pizzas e gelatos maravilhosos em praticamente todas as refeições. No geral, foi uma viagem super organizada, muito bem aproveitada e com lugares que realmente valem a pena conhecer.	5
6	1	Viagem para Paraty - RJ	Paraty - RJ	2024-11-20	2024-11-22	A viagem para Paraty foi excelente! O destino foi perfeito para comemorar meu aniversário e o roteiro combinou muito bem o charme histórico com passeios pela natureza.\r\nNo Centro Histórico, o destaque foi caminhar pelas ruas de pedra "pé de moleque" para ver o casario colonial, as igrejas antigas e as lojinhas de artesanato, tudo com um clima super agradável para celebrar a data. Outro ponto alto foi o passeio de barco pelas ilhas e praias da região, como a Praia da Lula e a Ilha Comprida, com águas transparentes ótimas para mergulho. Também deu para aproveitar as cachoeiras do entorno, como a do Tobogã, e fazer uma parada nos alambiques tradicionais para um brinde especial de aniversário.\r\nA gastronomia da cidade é maravilhosa, garantindo ótimos jantares de comemoração com frutos do mar e cafés charmosos para os fins de tarde. No geral, foi uma viagem super legal, muito bem aproveitada e uma forma inesquecível de passar o aniversário.\r\n	5
7	1	Viagem para Lençóis Maranhenses - MA	Lençóis Maranhenses - MA	2022-03-05	2022-03-15	A viagem para os Lençóis Maranhenses foi excelente, nota quatro de cinco. O destino é um dos lugares mais bonitos do país e o roteiro garantiu paisagens que parecem de outro mundo.\r\nO grande destaque foi caminhar pelas dunas imensas e mergulhar nas lagoas de água doce e cristalina, como a Lagoa Azul e a Lagoa do Bonito, o que é uma experiência inacreditável. O passeio de barco pelo Rio Preguiças também valeu muito a pena, com paradas em Vassouras e no farol de Mandacaru. A viagem só não levou nota máxima porque os deslocamentos nos veículos Toyota 4x4 chacoalham demais na areia e o calor extremo com caminhada no sol forte tornam os passeios fisicamente bem cansativos.\r\nA culinária local é deliciosa, especialmente os pratos com camarão e os sucos de frutas típicas como o cupuaçu. No geral, foi uma viagem super legal e com um visual único, perfeita para quem curte natureza, só exigindo um pouco mais de preparo físico para aguentar o ritmo.	4
8	1	Viagem para Arraial do cabo - RJ	Arraial do cabo - RJ	2021-05-10	2021-05-15	A viagem para Arraial do Cabo foi bem complicada, nota dois de cinco. O destino tem praias com águas transparentes que são realmente bonitas, mas a experiência geral foi bastante estressante e desorganizada.\r\nO maior problema foi a superlotação extrema na cidade e nas praias, o que gerou filas gigantescas de horas apenas para conseguir embarcar nos passeios de barco. O trânsito para chegar aos locais estava travado e foi quase impossível encontrar vaga para estacionar. Além disso, a estrutura de atendimento nos quiosques e restaurantes deixou muito a desejar, com serviços demorados, preços abusivos por conta da alta temporada e praias muito barulhentas, tirando toda a paz do lugar.\r\nO visual das praias como a do Forno e as Prainhas do Pontal do Atalaia até salvou algumas fotos, mas o estresse acumulado com a falta de infraestrutura e o empurra-empurra nas atrações não compensou. No geral, foi uma viagem cansativa, mal aproveitada e que infelizmente não pretendo repetir.\r\n	2
9	1	Viagem para Finlândia	Finlândia	2025-11-20	2025-12-10	A viagem para a Finlândia foi espetacular, nota cinco de cinco. O país entrega uma organização impecável e o roteiro na região da Lapônia garantiu uma das experiências mais mágicas e bem aproveitadas da vida.\r\nO grande destaque da viagem foi conseguir assistir à Aurora Boreal colorindo o céu noturno, um espetáculo impressionante e inesquecível. O dia a dia foi repleto de passeios fantásticos pela neve, como o safári em trenó puxado por huskies e a visita à charmosa Vila do Papai Noel em Rovaniemi. Para completar a experiência cultural, a tradicional sauna finlandesa seguida por um mergulho rápido na água gelada foi incrivelmente revigorante.\r\nA infraestrutura do país é perfeita, com transporte pontual, ambientes internos extremamente aquecidos e hotéis confortáveis com tetos de vidro para observar as estrelas. No geral, foi uma viagem maravilhosa, sem nenhum defeito e que vale absolutamente cada centavo pelo visual único e pela atmosfera de conto de fadas.	5
10	1	Viagem para Itália	Itália	2025-05-30	2025-06-20	A viagem para a Itália foi INCRÍVEL! Foi uma viagem longa, de 20 dias, o que foi perfeito para conhecer tudo com calma, sem pressa, e aproveitar de verdade cada destino do roteiro.\r\nO grande destaque dessa jornada foi a visita ao Vaticano, onde deu para se impressionar com a grandiosidade da Basílica de São Pedro e ver de perto os detalhes riquíssimos da Capela Sistina nos Museus do Vaticano. Além disso, o tempo estendido permitiu explorar a fundo os monumentos históricos de Roma, caminhar pelas colinas e vinícolas da Toscana, e ainda curtir o visual único dos canais de Veneza e o charme da Costa Amalfitana.\r\nA gastronomia ao longo desses 20 dias foi um show à parte, permitindo provar pratos típicos de diferentes regiões do país em jantares maravilhosos. No geral, por ser uma viagem mais longa, deu para aproveitar muito bem cada lugar, tornando a experiência super completa, organizada e inesquecível.	5
12	1	Viagem para Londres -  Inglaterra	Londres -  Inglaterra	2019-04-20	2019-04-28	A viagem para Londres foi espetacular! nota cinco de cinco. Foi uma viagem longa de 20 dias, o que foi perfeito para explorar a cidade no detalhe, conhecer tudo com muita calma e ainda fazer alguns bate-voltas incríveis pelos arredores.\r\nO grande destaque foi turistar pelos cartões-postais clássicos, como ver o Big Ben e o Parlamento, andar na London Eye e acompanhar a Troca da Guarda no Palácio de Buckingham. O tempo estendido permitiu aproveitar sem pressa os museus gratuitos — que são fantásticos —, como o British Museum e o Natural History Museum, além de caminhar bastante pelos parques reais como o Hyde Park. Também deu para aproveitar a vibe cultural dos mercados de rua, como o de Camden Town e o Borough Market, ótimos para comer e passeiar.\r\nA infraestrutura de transporte com o metrô facilitou demais o dia a dia, tornando os deslocamentos super rápidos e organizados. No geral, esses 20 dias foram maravilhosos, muito bem aproveitados e uma experiência completa que fez valer cada segundo na capital britânica.	5
13	1	Viagem para Japão	Japão	2018-07-10	2018-07-30	A viagem para o Japão foi espetacular, nota cinco de cinco. O destino é impressionante e o roteiro superou todas as expectativas, entregando um choque cultural positivo e cenários que parecem de outro mundo.\r\nO grande destaque foi circular por Tóquio e ver de perto o cruzamento de Shibuya, além dos letreiros iluminados de Shinjuku e a cultura pop de Akihabara. Outro ponto alto foi pegar o trem-bala rumo a Kyoto para conhecer os templos antigos, como o Pavilhão de Ouro, e caminhar pelas florestas de bambu de Arashiyama. Para fechar com chave de ouro, o passeio até a região do Monte Fuji garantiu uma das vistas mais bonitas e icônicas de toda a viagem.\r\nA culinária local foi uma experiência fantástica, com ramens maravilhosos, guiozas e espetinhos em mercados de rua super movimentados. Toda a viagem funcionou de forma impecável, com cidades extremamente limpas, transporte público pontual e um povo muito educado. No geral, foi uma experiência fantástica, super bem aproveitada e que deixa muita vontade de voltar.	5
\.


--
-- Name: fotos_viagem_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.fotos_viagem_id_seq', 65, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 1, true);


--
-- Name: viagens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.viagens_id_seq', 25, true);


--
-- Name: fotos_viagem fotos_viagem_pkey; Type: CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.fotos_viagem
    ADD CONSTRAINT fotos_viagem_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: viagens viagens_pkey; Type: CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.viagens
    ADD CONSTRAINT viagens_pkey PRIMARY KEY (id);


--
-- Name: fotos_viagem fk_fotos_viagem; Type: FK CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.fotos_viagem
    ADD CONSTRAINT fk_fotos_viagem FOREIGN KEY (id_viagem) REFERENCES public.viagens(id) ON DELETE CASCADE;


--
-- Name: viagens fk_viagens_usuario; Type: FK CONSTRAINT; Schema: public; Owner: travely
--

ALTER TABLE ONLY public.viagens
    ADD CONSTRAINT fk_viagens_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict SQF8RY7eRut7xcESeRFjOe8uOIMlq60GTJbdciFC7WqB366gQXCFLVCOI3uGJaz

