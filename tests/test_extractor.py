import importlib.util
from pathlib import Path
p=Path('assignment-01-web-php-http/source-analysis/extractor.py')
spec=importlib.util.spec_from_file_location('extractor',p);m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
def test_fixture():
 x=m.Extract();x.feed(Path('assignment-01-web-php-http/source-analysis/fixture.html').read_text());assert x.forms[0]['method']=='post';assert x.forms[0]['inputs'][0]['hidden'];assert x.comments
