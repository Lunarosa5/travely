--
-- PostgreSQL database dump
--

\restrict fOrXtk6QYkwlLoDeDgjJJBpu8Cxo9CPggRXUxj5TKfsMP4JiBrSz9XW0pTsYnUw

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
1	4	foto_1791402007.png
2	5	foto_1791545809.png
4	10	foto_1791566108.png
5	11	foto_1791566148.jpg
6	12	foto_1791566971.jpg
7	13	foto_1791567498.png
8	14	foto_1791567860_0.jpg
9	14	foto_1791567860_1.jpg
10	14	foto_1791567860_2.jpg
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: travely
--

COPY public.usuarios (id, nome, email, senha) FROM stdin;
1	Luna	teste@gmail.com	$2y$12$Qg5496fRFfxUj2eV1AUb3OdqmNWnhK1wHLcgjRqcZWSH/44AZQenW
2	userleticia	byleticia1@gmail.com	$2y$12$zWhB/nwu5Xm6DZiOQQphS./H97FMaWVoSxTKpR2JTlwjqTh8GPCpO
3	TigoFigo	Tigofigo@gmail.com	$2y$12$vy3I01od3Dvt5XwiaxjEu.kx.lZg5yUDgtIBdvyYvTYnLTuG5ikEe
4	Camile Tambolato	tambolatocamile@gmail.com	$2y$12$roxjXply0r1hp8feKPH7j.4Ltz4MxFCLS6IqLzIGBuWkA6PUQ.B3q
5	ana	anauribe.senai@gmail.com	$2y$12$7RMORL9gbJBP93/GPv5Y1OMEufOsI1JsJRbMFn0enacg92A745a.q
6	Alef Marcos 	alef@gmail.com	$2y$12$QHQbQgfUM7UIYUXFZVggbephBzrSK97LbzXshQ7pY60DZlWEeLN2a
7	Luna	luna@gmail.com	$2y$12$TKxVVSI7n5jLoIzJ55ZgZubgmM.bxtLC4E/pJGXkt4IesaEGtC.r.
\.


--
-- Data for Name: viagens; Type: TABLE DATA; Schema: public; Owner: travely
--

COPY public.viagens (id, id_usuario, titulo, destino, data_inicio, data_fim, relato, avaliacao) FROM stdin;
2	1	Viagem para Bahia	Bahia	2023-09-08	2023-09-15	Muito legal.	5
3	2	Viagem para Japão	Japão	2222-02-22	22222-12-22	kakskaks	4
4	1	Viagem para Japão	Japão	2020-11-22	2020-11-30	aaa	5
5	1	Viagem para São Paulo	São Paulo	2020-10-20	2020-11-20	sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654sdftgyuhrtdesdrftgyhujiuyt654	1
8	1	Viagem para sdrftgyhu	sdrftgyhu	2020-10-20	2021-02-10	sdfgbnjm	5
10	1	Viagem para sdrftgydssafd	sdrftgydssafd	2020-10-20	2020-10-30	hfvdcsxazDFFFD	5
11	1	Viagem para lalalalalala	lalalalalala	2020-10-10	2021-02-20	lalalalalalalalalalalalalalalalalalalalalalalalalalalalalala	3
12	1	Viagem para Belo Horizonte	Belo Horizonte	2025-06-15	2025-07-10	serdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6tserdtfyghujiuytrdtfghbjniu87y6t	5
13	1	Viagem para Malta	Malta	2023-07-11	2024-08-04	esdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhygesdftgyhujiuygtfcvgbhjnhyg	5
14	1	Viagem para Paris	Paris	2024-02-20	2024-04-10	szdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuhszdxfghujiuhygtfvbnmkjiuh	5
15	3	Viagem para Bahia	Bahia	2018-02-02	2019-02-02	foi forty, arrasei be. Chamine de hotdog encontrei la \r\nDisse a miga lurdes - "UOUUUU"	4
16	4	Viagem para Itália	Itália	2026-10-01	2026-10-09	oi Italia	5
19	7	Viagem para Trindade	Trindade	2026-09-10	2026-09-20	Legal!!!!!	5
\.


--
-- Name: fotos_viagem_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.fotos_viagem_id_seq', 10, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 7, true);


--
-- Name: viagens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: travely
--

SELECT pg_catalog.setval('public.viagens_id_seq', 19, true);


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

\unrestrict fOrXtk6QYkwlLoDeDgjJJBpu8Cxo9CPggRXUxj5TKfsMP4JiBrSz9XW0pTsYnUw

