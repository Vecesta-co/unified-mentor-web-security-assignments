import importlib.util
from pathlib import Path
def load(name):
 p=Path('assignment-04-sqli/scripts')/(name+'.py');s=importlib.util.spec_from_file_location(name,p);m=importlib.util.module_from_spec(s);s.loader.exec_module(m);return m
def test_boolean_mock():
 secret='cat';m=load('blind_boolean');assert m.extract(lambda pos,ch:pos<=len(secret) and secret[pos-1]==ch,alphabet='cat',delay=0,max_len=5)=='cat'
def test_cap():
 m=load('blind_boolean')
 try:m.extract(lambda p,c:False,alphabet='ab',delay=0,max_requests=1)
 except RuntimeError:pass
 else:assert False
